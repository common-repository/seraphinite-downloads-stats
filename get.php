<?php

namespace seraph_dlstat;

define( 'ABSPATH', '' );

require( __DIR__ . '/Cmn/Gen.php' );
require( __DIR__ . '/Cmn/Fs.php' );

$opDir = Gen::SanitizeTextData( (isset($_REQUEST[ 'seraph_dlstat_od' ])?$_REQUEST[ 'seraph_dlstat_od' ]:null) );
$secretKey = Gen::SanitizeId( (isset($_REQUEST[ 'seraph_dlstat_k' ])?$_REQUEST[ 'seraph_dlstat_k' ]:null) );
$uri = Gen::SanitizeTextData( (isset($_REQUEST[ 'seraph_dlstat_u' ])?$_REQUEST[ 'seraph_dlstat_u' ]:null) );
if( !$opDir || !$secretKey || !$uri )
{
	http_response_code( 400 );
	return;
}

$uri = ltrim( $uri, '/' );
$cfg = Gen::GetArrField( @unserialize( Gen::FileGetContentExclusive( $opDir . '/cfg.dat', '', true, 5000  ) ), array( '' ), array() );

if( $secretKey !== (isset($cfg[ 'secretKey' ])?$cfg[ 'secretKey' ]:null) || in_array( strtolower( Gen::GetFileExt( $uri ) ), array( 'htaccess' , 'conf' , 'php', 'js', 'css' ) ) )
{
	http_response_code( 403 );
	return;
}

Fs::StreamOutFileContent( (isset($cfg[ 'siteRootDir' ])?$cfg[ 'siteRootDir' ]:'') . substr( $uri, (isset($cfg[ 'siteRootPathLen' ])?$cfg[ 'siteRootPathLen' ]:0) ), Fs::MimeTypeDef, $_SERVER[ 'REQUEST_METHOD' ] != 'GET', 16384 );
Gen::CloseCurRequestSessionForContinueBgWork();

unset( $_REQUEST[ 'seraph_dlstat_u' ], $_REQUEST[ 'seraph_dlstat_od' ], $_REQUEST[ 'seraph_dlstat_k' ] );
_PostEvent( $opDir, $uri, $_REQUEST );

function _PostEvent( $opDir, $uri, $args )
{
	$ip = (isset($_SERVER[ 'REMOTE_ADDR' ])?$_SERVER[ 'REMOTE_ADDR' ]:null);
	{

	}

	$info = array();
	$info[ 'time' ] = Gen::GetCurRequestTime();
	$info[ 'ip' ] = $ip;
	$info[ 'user_agent' ] = (isset($_SERVER[ 'HTTP_USER_AGENT' ])?$_SERVER[ 'HTTP_USER_AGENT' ]:null);
	$info[ 'uri' ] = $uri;
	$info[ 'args' ] = $args;

	Gen::FileOpenWithMakeDir( $h, $opDir . '/q.dat.lst', 'ab' );
	if( !$h )
		return( false );

	if( !@flock( $h, LOCK_EX ) )
	{
		@fclose( $h );
		return( false );
	}

	if( @fwrite( $h, "\n" . str_replace( array( "\r", "\n" ), array( '{{{CR}}}', '{{{LF}}}' ), @serialize( $info ) ) ) === false )
	{
		@fclose( $h );
		return( false );
	}

	@fclose( $h );
	return( true );
}

