<?php

namespace seraph_dlstat;

if( !defined( 'ABSPATH' ) )
	exit;

require_once( __DIR__ . '/Cmn/Gen.php' );
require_once( __DIR__ . '/Cmn/Ui.php' );
require_once( __DIR__ . '/Cmn/Plugin.php' );
require_once( __DIR__ . '/Cmn/Fs.php' );
require_once( __DIR__ . '/Cmn/Db.php' );

const PLUGIN_SETT_VER								= 2;
const PLUGIN_DATA_VER								= 1;
const PLUGIN_EULA_VER								= 1;
const PLUGIN_SETT_PAGE_ID							= 'seraph_dlstat_settings';

function OnAsyncTasksGetFile()
{
	return( GetOpDir() . '/at.dat' );
}

add_filter( 'cron_schedules',		'seraph_dlstat\\_OnCronSchedules' );
add_filter( 'do_parse_request',		'seraph_dlstat\\_do_parse_request', 0, 1 );

function _do_parse_request( $continue )
{
	if( !wp_next_scheduled( 'seraph_dlstat_cron_hook' ) )
	{
		$sett = Plugin::SettGet();
		_ApplySchedule( $sett, true );
	}

	return( $continue );
}

const SCHEDULEINTERVALS_DEFVAL	= 120;

function _GetScheduleIntervals()
{
	$intervals = array(
		60		=> array( 'display' => esc_html_x( 'Min1', 'admin.Settings_SendMode_Cmb', 'seraphinite-downloads-stats' ), 'displayPluralVal' => 1 ),
		120		=> array( 'display' => esc_html_x( 'Min2', 'admin.Settings_SendMode_Cmb', 'seraphinite-downloads-stats' ), 'displayPluralVal' => 2 ),
		300		=> array( 'display' => esc_html_x( 'Min5', 'admin.Settings_SendMode_Cmb', 'seraphinite-downloads-stats' ), 'displayPluralVal' => 5 ),
		600		=> array( 'display' => esc_html_x( 'Min10', 'admin.Settings_SendMode_Cmb', 'seraphinite-downloads-stats' ), 'displayPluralVal' => 10 ),
	);

	return( $intervals );
}

function _OnCronSchedules( $schedules )
{
	foreach( _GetScheduleIntervals() as $v => $interval )
		$schedules[ 'seraph_dlstat_seconds_' . $v ] = array( 'interval' => $v, 'display'  => $interval[ 'display' ] );

	return( $schedules );
}

add_action( 'seraph_dlstat_cron_hook',
	function()
	{
		Plugin::AsyncTaskPost( 'ProcessPostponedEvents' );
	}
);

function OnActivate()
{
	$sett = Plugin::SettGet();
	Plugin::SettSet( $sett );
}

function OnDeactivate()
{
	$sett = Plugin::SettGet();

	_ApplyHtaccess( $sett, false );
	_ApplySchedule( $sett, false );
	_ApplyTbl( $sett, false );
}

function OnOptRead_Sett( $sett, $verFrom )
{
	$sett = apply_filters( 'seraph_dlstat_options_load', $sett, $verFrom );
	return( $sett );
}

function OnOptWritePrep_Sett( $sett )
{
	$sett = apply_filters( 'seraph_dlstat_options_save', $sett );

	_ApplyHtaccess( $sett, true );
	_ApplyConfig();
	_ApplySchedule( $sett, true );
	_ApplyTbl( $sett, true );

	return( $sett );
}

function GetSitePath()
{
	$siteUrlParts = Gen::GetArrField( Net::UrlParse( Wp::GetSiteRootUrl() ), array( '' ), array() );
	return( Gen::SetLastSlash( ltrim( (isset($siteUrlParts[ 'path' ])?$siteUrlParts[ 'path' ]:''), '/' ) ) );
}

function GetOpDir()
{
	return( str_replace( '\\', '/', WP_CONTENT_DIR ) . '/seraph_dlstat' );
}

function GetOpPath()
{
	return( Net::Url2Uri( content_url( 'seraph_dlstat' ) ) );
}

function _StrForHtAccess( $str )
{
	$res = '';

	foreach( str_split( $str ) as $c )
	{
		if( ctype_alpha( $c ) || ctype_digit( $c ) || $c == '-' || $c == '_' || $c == '.' || $c == '/' )
			$res .= $c;
		else
			$res .= sprintf( '\\x%02X', ord( $c ) );
	}

	return( Gen::HtAccess_QuoteUri( $res ) );
}

function _ApplyHtaccess( $sett, $apply )
{
	$pathSite = '/' . GetSitePath();

	$hr = Gen::S_OK;

	$htaccessBlock = '';
	$confBlock = "# Preventing from reading secret key from configurations\nlocation ~ ^" . Gen::HtAccess_QuoteUri( GetOpPath() . '/' ) . " {\n\tdeny all;\n}\nlocation ~ ^/seraph-dlstat\\.conf$ {\n\tdeny all;\n}\n\n# File hook redirections\n";

	if( $apply )
	{
		$items = Gen::GetArrField( $sett, 'items', array(), '/' );

		foreach( $items as $item )
		{
			if( !Gen::GetArrField( $item, 'enable', true, '/' ) )
				continue;

			$path = trim( Gen::GetArrField( $item, 'dataUrlPath', '', '/' ), '\\/' );
			if( !$path )
				continue;

			$path = _StrForHtAccess( $path );

			$inclFilesExts = Gen::GetArrField( $item, 'filesExtsIncl', array(), '/' );

			foreach( $inclFilesExts as $inclFileExtKey => $inclFileExt )
				$inclFilesExts[ $inclFileExtKey ] = _StrForHtAccess( $inclFileExt );

			$htaccessBlock .= '<IfModule mod_rewrite.c>' . "\n";
			$htaccessBlock .= "\t" . 'RewriteEngine On' . "\n";

			if( $inclFilesExts )
			{
				$htaccessBlock .= "\t" . 'RewriteCond %{REQUEST_FILENAME} \\.(' . implode( '|', $inclFilesExts ) . ')$' . "\n";
				$confBlock .= 'location ~ .*\.(' . implode( '|', $inclFilesExts ) . ')$ {' . "\n";
			}

			$htaccessBlock .= "\t" . 'RewriteCond %{REQUEST_URI} ^' . $pathSite . _StrForHtAccess( $path ) . '/(.*)' . "\n";
			$htaccessBlock .= "\t" . 'RewriteRule ^(.*) ' . Gen::HtAccess_QuoteUri( add_query_arg( array( 'seraph_dlstat_od' => GetOpDir(), 'seraph_dlstat_k' => md5( NONCE_SALT ), 'seraph_dlstat_u' => '%{REQUEST_URI}' ), Plugin::FileUri( 'get.php', __FILE__ ) ) ) . ' [QSA,END]' . "\n";
			$confBlock .= ( $inclFilesExts ? "\t" : '' ) . 'rewrite ^' . $pathSite . $path . '/ ' . add_query_arg( array( 'seraph_dlstat_od' => GetOpDir(), 'seraph_dlstat_k' => md5( NONCE_SALT ), 'seraph_dlstat_u' => '$uri' ), Plugin::FileUri( 'get.php', __FILE__ ) ) . ' last;' . "\n";

			if( $inclFilesExts )
			{
				$confBlock .= '}' . "\n";
			}

			$htaccessBlock .= '</IfModule>' . "\n";

		}

		$htaccessBlock = trim( $htaccessBlock );
		$confBlock = trim( $confBlock );
	}

	if( Gen::HtAccess_IsSupported() )
	{
		if( Gen::HtAccess_GetBlock( 'seraphinite-downloads-stats' ) != $htaccessBlock )
			$hr = Gen::HrAccom( $hr, Gen::HtAccess_SetBlock( 'seraphinite-downloads-stats', $htaccessBlock, 5 ) );
	}
	else
	{
		if( !$confBlock )
			$confBlock .= '# Empty' . "\n";

		$confBlock =
			'# seraphinite-downloads-stats' . "\n\n" .
			$confBlock .
			'';

		$fileConf = get_home_path() . 'seraph-dlstat.conf';
		if( @file_get_contents( $fileConf ) !== $confBlock )
			@file_put_contents( $fileConf, $confBlock );
	}

	return( $hr );
}

function _ApplyConfig()
{
	$opDir = GetOpDir();

	{
		$cfg = array();
		$cfg[ 'siteRootDir' ] = str_replace( '\\', '/', ABSPATH );
		$cfg[ 'siteRootPathLen' ] = strlen( GetSitePath() );
		$cfg[ 'secretKey' ] = md5( NONCE_SALT );

		Gen::FilePutContentExclusive( $opDir . '/cfg.dat', @serialize( $cfg ), true );
	}

	if( !@file_exists( $opDir . '/q.dat.lst' ) )
		@file_put_contents( $opDir . '/q.dat.lst', '' );
	if( !@file_exists( $opDir . '/at.dat' ) )
		@file_put_contents( $opDir . '/at.dat', '' );

	@file_put_contents( $opDir . '/.htaccess', "Order deny,allow\r\nDeny from all" );
}

function _ApplySchedule( $sett, $apply )
{
	$timestamp = wp_next_scheduled( 'seraph_dlstat_cron_hook' );

	if( $apply )
	{
		if( $timestamp )
			wp_unschedule_event( $timestamp, 'seraph_dlstat_cron_hook' );

		$timeInterval = 'seraph_dlstat_seconds_' . Gen::GetArrField( $sett, 'evtSendSchedInterval', SCHEDULEINTERVALS_DEFVAL, '/' );
		wp_schedule_event( time(), $timeInterval, 'seraph_dlstat_cron_hook' );
	}
	else
	{
		if( !$timestamp )
			return;

		wp_unschedule_event( $timestamp, 'seraph_dlstat_cron_hook' );
	}
}

function _ApplyTbl( $sett, $apply )
{
	DbTbl::Delete( Db::GetTblPrefix() . 'seraph_dlstat_evts_queue' );
	Plugin::StateUpdateFlds( array( 'evtsQueueDbVer' => null ) );
}

function _ProcessPostponedEvents( $sett )
{
	Gen::FileOpenWithMakeDir( $h, GetOpDir() . '/q.dat.lst', 'rb+' );
	if( !$h )
		return( null );

	if( !@flock( $h, LOCK_EX ) )
	{
		@fclose( $h );
		return( false );
	}

	$infos = array();
	while( ( $info = @fgets( $h ) ) !== false )
		if( $info = @unserialize( str_replace( array( '{{{CR}}}', '{{{LF}}}' ), array( "\r", "\n" ), rtrim( $info, "\r\n" ) ) ) )
			$infos[] = $info;
	unset( $info );

	if( !ftruncate( $h, 0 ) )
	{
		@fclose( $h );
		return( false );
	}

	@fclose( $h );
	unset( $h );

	if( $infos )
		_ProcessEvents( $sett, $infos );

	return( true );
}

function _GetSettItemByUri( $sett, $uri )
{
	foreach( $sett[ 'items' ] as $settItem )
	{
		if( strpos( $uri, $settItem[ 'dataUrlPath' ] ) === 0 )
			return( $settItem );
	}

	return( null );
}

function _ProcessEvents( $sett, $infos )
{
	$evtLblEnable = Gen::GetArrField( $sett, 'evtLblEnable', false, '/' );

	for( $i = 0, $n = count( $infos ); $i < $n; $i++ )
	{
		$info = $infos[ $i ];

		$settItem = _GetSettItemByUri( $sett, $info[ 'uri' ] );
		$settItemEvtLblEnable = $evtLblEnable ? Gen::GetArrField( $settItem, 'evtLblEnable', false, '/' ) : false;

		$info[ 'source' ] = $info[ 'ip' ];
		$info[ 'source_type' ] = 'ip';

		$infosTmp = array( $info );
		$infosTmp = apply_filters( 'seraph_dlstat_prepare_events', $infosTmp );

		for( $t = 0, $tn = count( $infosTmp ); $t < $tn; $t++ )
		{
			$info = $infosTmp[ $t ];

			{
				$strEvtLabel = '';
				if( $settItemEvtLblEnable )
				{
					$args = $info[ 'args' ];

					$strEvtLabel = apply_filters( 'seraph_dlstat_event_label_format', null, $args );
					if( !$strEvtLabel )
						$strEvtLabel = _GetEventLabel( $sett, $args );
				}

				$info[ 'label' ] = $strEvtLabel;
			}

			if( $t == 0 )
				$infos[ $i ] = $info;
			else
				$infos[] = $info;
		}
	}

	do_action( 'seraph_dlstat_items_download_requested', $infos, $sett );
}

function _GetEventLabel( $sett, $args )
{
	$evtLblPrmSep = Gen::GetArrField( $sett, 'evtLblPrmSep', '&', '/' );
	$evtLblPrmValSep = Gen::GetArrField( $sett, 'evtLblPrmValSep', '=', '/' );

	$strEvtLabel = '';

	foreach( $args as $arg => $argVal )
	{
		if( !empty( $strEvtLabel ) )
			$strEvtLabel .= $evtLblPrmSep;

		$strEvtLabel .= $arg;
		if( !empty( $argVal ) )
			$strEvtLabel .= $evtLblPrmValSep . $argVal;
	}

	return( $strEvtLabel );
}

function InfoItem_CanProcess( $info, $processorId )
{
	$processors = @$info[ 'processors' ];
	return( empty( $processors ) || isset( $processors[ $processorId ] ) );
}

function InfoItem_SetProcessors( &$info, $processorIds )
{
	$processors = array();

	if( !is_array( $processorIds ) )
		$processorIds = array( $processorIds );

	foreach( $processorIds as $processorId )
		$processors[ $processorId ] = true;

	$info[ 'processors' ] = $processors;
}

function OnAsyncTask_ProcessPostponedEvents( $args )
{
	@set_time_limit( 60 * 60 );

	_ProcessPostponedEvents( Plugin::SettGet() );
}

