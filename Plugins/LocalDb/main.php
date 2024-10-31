<?php

namespace seraph_dlstat;

if( !defined( 'ABSPATH' ) )
	exit;

const LDB_DB_VERSION					= 3;

add_action( 'seraph_dlstat_settings_display',			'seraph_dlstat\\_ldb_settings_display', 10, 1 );
add_filter( 'seraph_dlstat_onSettingsSave',				'seraph_dlstat\\_OnLdbSettingsSave', 10, 3 );

add_filter( 'seraph_dlstat_options_load',				'seraph_dlstat\\_ldb_options_load', 10, 2 );
add_filter( 'seraph_dlstat_options_save',				'seraph_dlstat\\_ldb_options_save', 10, 1 );

add_action( 'seraph_dlstat_items_download_requested',	'seraph_dlstat\\_ldb_items_download_requested', 10, 2 );

function _ldb_settings_display( $sett )
{
	$rmtCfg = PluginRmtCfg::Get();

	Ui::PostBoxes_MetaboxAdd( 'ldb', esc_html_x( 'Title', 'admin.Settings_Ldb', 'seraphinite-downloads-stats' ) . Ui::Tag( 'span', Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_Ldb' ), Ui::AdminHelpBtnModeBlockHeader ) ), true,
		function( $callbacks_args, $box )
		{
			extract( $box[ 'args' ] );

?>

			<?php echo( Ui::SettBlock_Begin() ); ?>
			
				<?php echo( Ui::SettBlock_Item_Begin( esc_html_x( 'EnableLbl', 'admin.Settings_Ldb', 'seraphinite-downloads-stats' ) . Ui::Tag( 'span', Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_Ldb_Enable' ) ) ) ) ); ?>
					<?php $fldId = 'plugins/Ldb/enable'; echo( Ui::CheckBox( null, $fldId, Gen::GetArrField( $sett, $fldId, false, '/' ), true ) ); ?>
				<?php echo( Ui::SettBlock_Item_End() ); ?>
			
				<?php echo( Ui::SettBlock_Item_Begin( esc_html_x( 'DataLbl', 'admin.Settings_Ldb', 'seraphinite-downloads-stats' ) . Ui::Tag( 'span', Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_Ldb_Data' ) ) ) ) ); ?>
					<?php $fldId = 'plugins/Ldb/clear'; echo( Ui::CheckBox( esc_html_x( 'DataClearChk', 'admin.Settings_Ldb', 'seraphinite-downloads-stats' ), $fldId, Gen::GetArrField( $sett, $fldId, false, '/' ), true ) ); ?>
				<?php echo( Ui::SettBlock_Item_End() ); ?>
							
			<?php echo( Ui::SettBlock_End() ); ?>
	
			<?php

			echo( Ui::Tag( 'p', sprintf( esc_html_x( 'NoteStr_%1$s', 'admin.Settings_Ldb', 'seraphinite-downloads-stats' ), Db::GetTblPrefix( 'seraph_dlstat' ) ), array( 'class' => 'description ctlSpaceVBefore' ) ) );
		},
		get_defined_vars()
	);
}

function _OnLdbSettingsSave( $hr, $args, &$sett )
{
	{ $fldId = 'plugins/Ldb/enable'; Gen::SetArrField( $sett, $fldId, isset( $args[ $fldId ] ), '/' ); }

	$clearDb = isset( $args[ 'plugins/Ldb/clear' ] );
	if( $clearDb )
	{
		if( Gen::GetArrField( $sett, 'plugins/Ldb/enable', false, '/' ) )
			DbTbl::DeleteRows( Db::GetTblPrefix( 'seraph_dlstat' ) );
		else
		{
			DbTbl::Delete( Db::GetTblPrefix( 'seraph_dlstat' ) );

			$data = Plugin::DataGet();
			Gen::SetArrField( $data, 'plugins.Ldb.dbVer', 0 );
			Plugin::DataSet( $data );
		}
	}

	return( Gen::S_OK );
}

function _ldb_options_load( $sett, $verFrom )
{
	return( $sett );
}

function _ldb_options_save( $sett )
{
	if( Gen::GetArrField( $sett, 'plugins/Ldb/enable', false, '/' ) )
	{
		$data = Plugin::DataGet();

		$fldId = 'plugins.Ldb.dbVer';
		if( Gen::GetArrField( $data, $fldId, 0 ) != LDB_DB_VERSION )
		{
			DbTbl::CreateUpdate( Db::GetTblPrefix( 'seraph_dlstat' ), array(
				'id'		=> array( 'type' => 'INT(12)', 'attrs' => array( 'UNSIGNED', 'NOT NULL', 'AUTO_INCREMENT', 'PRIMARY KEY' ) ),
				'hash'		=> array( 'type' => 'VARCHAR(255)', 'attrs' => array( 'NOT NULL', 'INDEX' ) ),
				'source'	=> array( 'type' => 'VARCHAR(255)', 'attrs' => array( 'NOT NULL', 'INDEX' ) ),
				'ip'		=> array( 'type' => 'VARCHAR(255)', 'attrs' => array( 'NOT NULL', 'INDEX' ) ),
				'uri'		=> array( 'type' => 'TEXT' ),
				'label'		=> array( 'type' => 'TEXT' ),
				'time'		=> array( 'type' => 'DATETIME', 'attrs' => array( 'INDEX' ) ),
				'count'		=> array( 'type' => 'INT(12)', 'attrs' => array( 'UNSIGNED' ) ),
			) );

			Gen::SetArrField( $data, $fldId, LDB_DB_VERSION );

			Plugin::DataSet( $data );
		}
	}

	return( $sett );
}

function _ldb_items_download_requested( $infos, $sett )
{
	global $wpdb;

	if( !Gen::GetArrField( $sett, 'plugins/Ldb/enable', false, '/' ) )
		return;

	foreach( $infos as $info )
	{
		if( !InfoItem_CanProcess( $info, "ldb" ) )
			continue;

		$sourceType = $info[ 'source_type' ];
		$source = $sourceType . ( ( $sourceType == 'ip' ) ? '' : ( ':' . $info[ 'source' ] ) );

		$data = array( 'hash' => md5( $source . $info[ 'ip' ] . $info[ 'uri' ] . $info[ 'label' ] ) );

		{
			$dbtran = new Lock( 'db_l', GetOpDir() );
			if( !$dbtran -> Acquire() )
				return;

			$item = $wpdb -> get_row( 'SELECT id,count FROM ' . Db::GetTblPrefix( 'seraph_dlstat' ) . ' WHERE hash=\'' . $data[ 'hash' ] . '\'', ARRAY_A );
			if( $item )
				$wpdb -> update( Db::GetTblPrefix( 'seraph_dlstat' ), array( 'count' => $item[ 'count' ] + 1, 'time' => gmdate( DATE_ATOM, $info[ 'time' ] ) ), array( 'id' => $item[ 'id' ] ) );
			else
			{
				$sourceType = $info[ 'source_type' ];

				$data[ 'source' ] = $source;
				$data[ 'ip' ] = $info[ 'ip' ];
				$data[ 'uri' ] = $info[ 'uri' ];
				$data[ 'label' ] = $info[ 'label' ];
				$data[ 'time' ] = gmdate( DATE_ATOM, $info[ 'time' ] );
				$data[ 'count' ] = 1;
				$wpdb -> insert( Db::GetTblPrefix( 'seraph_dlstat' ), $data );
			}

			unset( $dbtran );
		}
	}
}

