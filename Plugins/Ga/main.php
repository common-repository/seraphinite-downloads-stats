<?php

namespace seraph_dlstat;

if( !defined( 'ABSPATH' ) )
	exit;

add_action( 'seraph_dlstat_settings_display',			'seraph_dlstat\\_ga_settings_display', 10, 1 );
add_filter( 'seraph_dlstat_onSettingsSave',				'seraph_dlstat\\_OnGaSettingsSave', 10, 3 );

add_filter( 'seraph_dlstat_options_load',				'seraph_dlstat\\_ga_options_load', 10, 2 );
add_filter( 'seraph_dlstat_options_save',				'seraph_dlstat\\_ga_options_save', 10, 1 );

add_action( 'seraph_dlstat_items_download_requested',	'seraph_dlstat\\_ga_items_download_requested', 10, 2 );

function _ga_settings_display( $sett )
{
	$rmtCfg = PluginRmtCfg::Get();

	Ui::PostBoxes_MetaboxAdd( 'ga', esc_html_x( 'Title', 'admin.Settings_Ga', 'seraphinite-downloads-stats' ) . Ui::Tag( 'span', Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_Ga' ), Ui::AdminHelpBtnModeBlockHeader ) ), true,
		function( $callbacks_args, $box )
		{
			extract( $box[ 'args' ] );

			echo( Ui::SettBlock_Begin() );
			{
				echo( Ui::SettBlock_Item_Begin( esc_html_x( 'EnableLbl', 'admin.Settings_Ga', 'seraphinite-downloads-stats' ) . Ui::Tag( 'span', Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_Ga_Enable' ) ) ) ) );
				$fldId = 'plugins/Ga/enable'; echo( Ui::CheckBox( null, $fldId, Gen::GetArrField( $sett, $fldId, false, '/' ), true ) );
				echo( Ui::SettBlock_Item_End() );

				echo( Ui::SettBlock_Item_Begin( esc_html_x( 'TrackIdLbl', 'admin.Settings_Ga', 'seraphinite-downloads-stats' ) . Ui::Tag( 'span', Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_Ga_TrackId' ) ) ) ) );
				$fldId = 'plugins/Ga/trackId'; echo( Ui::TextBox( $fldId, Gen::GetArrField( $sett, $fldId, '', '/' ), array( 'size' => 30, 'placeholder' => 'UA-XXXXXXXX-X' ), true ) );
				echo( Ui::SettBlock_Item_End() );

				echo( Ui::SettBlock_Item_Begin( esc_html_x( 'EvCatLbl', 'admin.Settings_Ga', 'seraphinite-downloads-stats' ) . Ui::Tag( 'span', Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_Ga_EvCat' ) ) ) ) );
				$fldId = 'plugins/Ga/evtCat'; echo( Ui::TextBox( $fldId, Gen::GetArrField( $sett, $fldId, '', '/' ), array( 'size' => 60 ), true ) );
				echo( Ui::SettBlock_Item_End() );

				echo( Ui::SettBlock_Item_Begin( esc_html_x( 'CusDimCliIdLbl', 'admin.Settings_Ga', 'seraphinite-downloads-stats' ) . Ui::Tag( 'span', Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_Ga_CusDimCliId' ) ) ) ) );
				$fldId = 'plugins/Ga/cdIdxForCid'; echo( Ui::NumberBox( $fldId, Gen::GetArrField( $sett, $fldId, null, '/' ), array( 'size' => 10, 'min' => 0, 'placeholder' => _x( 'CusDimCliIdEdtPh', 'admin.Settings_Ga', 'seraphinite-downloads-stats' ) ), true ) );
				echo( Ui::SettBlock_Item_End() );

			}
			echo( Ui::SettBlock_End() );
		},
		get_defined_vars()
	);
}

function _OnGaSettingsSave( $hr, $args, &$sett )
{
	{ $fldId = 'plugins/Ga/enable';		Gen::SetArrField( $sett, $fldId, isset( $args[ $fldId ] ), '/' ); }
	{ $fldId = 'plugins/Ga/trackId';	Gen::SetArrField( $sett, $fldId, trim( $args[ $fldId ] ), '/' ); }
	{ $fldId = 'plugins/Ga/evtCat';		Gen::SetArrField( $sett, $fldId, trim( $args[ $fldId ] ), '/' ); }

	{
		$fldId = 'plugins/Ga/cdIdxForCid';
		$fldIdVal = $args[ $fldId ];
		$fldIdVal = empty( $fldIdVal ) ? null : intval( $fldIdVal );

		Gen::SetArrField( $sett, $fldId, $fldIdVal, '/' );
	}

	return( Gen::S_OK );
}

function _ga_options_load( $sett, $verFrom )
{
	{
		$fldId = 'plugins/Ga/evtCat';
		if( !Gen::GetArrField( $sett, $fldId, null ) )
			Gen::SetArrField( $sett, $fldId, 'DownloadItems', '/' );
	}

	return( $sett );
}

function _ga_options_save( $sett )
{
	return( $sett );
}

function _ga_items_download_requested( $infos, $sett )
{
	if( !Gen::GetArrField( $sett, 'plugins/Ga/enable', false, '/' ) )
		return;

	$trackId = Gen::GetArrField( $sett, 'plugins/Ga/trackId', null, '/' );
	if( empty( $trackId ) )
		return;

	$evtCat = Gen::GetArrField( $sett, 'plugins/Ga/evtCat', 'DownloadItems', '/' );
	$cdIdxForCid = Gen::GetArrField( $sett, 'plugins/Ga/cdIdxForCid', null, '/' );
	$batch = Gen::GetArrField( $sett, 'plugins/Ga/batch', false, '/' );

	$batchBody = '';

	if( $batch )
		$batchBoundary = md5( '' . Gen::GetCurRequestTime() );

	foreach( $infos as $info )
	{
		if( !InfoItem_CanProcess( $info, "ga" ) )
			continue;

		$clientId = '';
		{
			$source_type = @$info[ 'source_type' ];
			if( !empty( $source_type ) )
				$clientId .= $source_type . ':';
		}
		$clientId .= $info[ 'source' ];

		$args = array(
			'v' => 1,
			'qt' => ( time() - $info[ 'time' ] ) * 1000,
			'tid' => $trackId,
			'cid' => $clientId,
			'uip' => $info[ 'ip' ],
			'ua' => rawurlencode( $info[ 'user_agent' ] ),
			't' => 'event',
			'ec' => rawurlencode( $evtCat ),
			'ea' => rawurlencode( $info[ 'uri' ] )
		);

		if( $cdIdxForCid !== null )
			$args[ 'cd' . $cdIdxForCid ] = $clientId;

		{
			$label = @$info[ 'label' ];
			if( !empty( $label ) )
				$args[ 'el' ] = rawurlencode( $label );
		}

		$args = apply_filters( 'seraph_dlstat_ga_prepare_event', $args, $info );

		$query = add_query_arg( $args, 'https://www.google-analytics.com/collect' );
		if( $batch )
		{
			$batchBody .= "--" . $batchBoundary . "\r\nContent-Type: application/http\r\nContent-Transfer-Encoding: binary\r\n\r\nPOST " . $query . "\r\nContent-type: text/plain\r\nContent-Length: 0\r\n\r\n";

		}
		else
			wp_remote_post( $query );
	}

	if( !$batchBody )
		return;

	$batchBody .= "--" . $batchBoundary . '--';

	$res = wp_remote_post( 'https://www.googleapis.com/batch/analytics/v3', array( 'headers' => array( 'Content-type: multipart/mixed; boundary="' . $batchBoundary . '"' ), 'body' => $batchBody ) );
}

