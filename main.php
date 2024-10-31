<?php

namespace seraph_dlstat;

if( !defined( 'ABSPATH' ) )
	exit;

include( "common.php" );

$files = glob( __DIR__ . '/Plugins/*/main.php' );
foreach( $files as $filename )
    include( $filename );

Plugin::Init();

function _AddMenus( $accepted = false )
{
	add_options_page( Plugin::GetSettingsTitle(), Plugin::GetNavMenuTitle(), 'manage_options', PLUGIN_SETT_PAGE_ID, $accepted ? 'seraph_dlstat\\_SettingsPage' : 'seraph_dlstat\\Plugin::OutputNotAcceptedPageContent' );
}

function OnInitAdminModeNotAccepted()
{
	add_action( 'admin_menu',
		function()
		{
			_AddMenus();
		}
	);
}

function OnInitAdminMode()
{
	add_action( 'admin_init',
		function()
		{
			if( isset( $_POST[ 'seraph_dlstat_saveSettings' ] ) )
			{
				unset( $_POST[ 'seraph_dlstat_saveSettings' ] );
				Plugin::ReloadWithPostOpRes( array( 'saveSettings' => _OnSaveSettings( $_POST ) ) );
				exit;
			}
		}
	);

	add_action( 'seraph_dlstat_postOpsRes',
		function( $res )
		{
			if( ( $hr = @$res[ 'saveSettings' ] ) !== null )
				echo( Plugin::Sett_SaveResultBannerMsg( $hr, Ui::MsgOptDismissible ) );
		}
	);

	add_action( 'admin_menu',
		function()
		{
			_AddMenus( true );
		}
	);

	add_action( 'admin_notices',
		function()
		{
			Plugin::_admin_printscriptsstyles();

			foreach( array( '*', 'cfg.dat', 'q.dat.lst', 'at.dat' ) as $file )
			{
				if( @is_writable( GetOpDir() . '/' . ( $file === '*' ? '' : $file ) ) )
					continue;

				echo( Ui::BannerMsg( Ui::MsgErr, Ui::Tag( 'strong', Plugin::GetPluginString( 'TitleFull' ) ) . Ui::Tag( 'p', sprintf( Wp::safe_html_x( 'DataDirNotWrittable_%1$s%2$s', 'admin.Notice', 'seraphinite-downloads-stats' ), GetOpDir(), $file ) ) ) );
				break;
			}
		}
	);
}

function _SettOutputTokensEditor( $fldId, $v, $placeholder, $ns, $sep = ',', $height = 5, $masked = false )
{
	echo( Ui::TokensList( $v, $ns . '/' . $fldId, array( 'masked' => $masked, 'class' => 'vals ctlSpaceVAfter', 'style' => array( 'min-height' => '3em', 'height' => '' . $height . 'em', 'max-height' => '20em' ), 'data-oninit' => 'seraph_dlstat.Ui.TokensList.InitItems( this, true )' ), true ) );

	echo( Ui::SettBlock_ItemSubTbl_Begin( array( 'class' => 'std', 'style' => array( 'width' => '100%' ) ) ) . Ui::TagOpen( 'tr' ) );
	{
		echo( Ui::Tag( 'td', Ui::TextBox( null, '', array( 'class' => 'val', 'placeholder' => $placeholder, 'style' => array( 'width' => '100%' ) ) ) ) );
		echo( Ui::Tag( 'td', Ui::Button( esc_html( Wp::GetLocString( array( 'AddItemBtn', 'admin.Common_ItemsList' ), null, 'seraphinite-downloads-stats' ) ), false, null, null, 'button', array( 'onclick' => 'seraph_dlstat.Settings._int.StrItem_OnAdd( this, "' . $sep . '" ); return false;' ) ), array( 'style' => array( 'width' => '1px' ) ) ) );
	}
	echo( Ui::TagClose( 'tr' ) . Ui::SettBlock_ItemSubTbl_End() );
}

function _SettingsPage()
{
	Plugin::CmnScripts( array( 'Cmn', 'Gen', 'Ui', 'Net', 'AdminUi' ) );
	wp_register_script( Plugin::ScriptId( 'Admin' ), add_query_arg( Plugin::GetFileUrlPackageParams(), Plugin::FileUrl( 'Admin.js', __FILE__ ) ), array_merge( array( 'jquery' ), Plugin::CmnScriptId( array( 'Cmn', 'Gen', 'Ui', 'Net' ) ) ), '1.3.1' );
	Plugin::Loc_ScriptLoad( Plugin::ScriptId( 'Admin' ) );
	wp_enqueue_script( Plugin::ScriptId( 'Admin' ) );

	Plugin::DisplayAdminFooterRateItContent();

	$rmtCfg = PluginRmtCfg::Get();
	$sett = Plugin::SettGet();

	{
		Ui::PostBoxes_MetaboxAdd( 'general', esc_html_x( 'Title', 'admin.Settings_General', 'seraphinite-downloads-stats' ) . Ui::Tag( 'span', Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings' ), Ui::AdminHelpBtnModeBlockHeader ) ), true,
			function( $callbacks_args, $box )
			{
				extract( $box[ 'args' ] );

				echo( Ui::SettBlock_Begin() );
				{
					echo( Ui::SettBlock_Item_Begin( esc_html_x( 'Lbl', 'admin.Settings_Items', 'seraphinite-downloads-stats' ) ) );
					{
						$fldId = 'items';
						$items = Gen::GetArrField( $sett, $fldId, array(), '/' );

						if( empty( $items ) )
							$items[] = array();

						$itemsListPrms = array( 'editorAreaCssPath' => '#general', 'sortable' => true );

						echo( Ui::ItemsList( $itemsListPrms, $items, $fldId,
							function( $cbArgs, $idItems, $items, $itemKey, $item )
							{
								extract( $cbArgs );

								ob_start();

								echo( Ui::SettBlock_ItemSubTbl_Begin( array( 'class' => 'ctlMaxSizeX block' ) ) );
								{
									{
										$fldId = 'enable';
										echo( Ui::Tag( 'tr', Ui::Tag( 'td', Ui::CheckBox( esc_html_x( 'EnableChk', 'admin.Settings_Items', 'seraphinite-downloads-stats' ) . Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_Item_Enable' ), Ui::AdminHelpBtnModeChkRad ), $idItems . '/' . $itemKey . '/' . $fldId, Gen::GetArrField( $item, $fldId, true, '/' ), true ) ) ) );
									}

									{
										$fldId = 'evtLblEnable';
										echo( Ui::Tag( 'tr', Ui::Tag( 'td', Ui::CheckBox( esc_html_x( 'EvtLblEnableChk', 'admin.Settings_Items', 'seraphinite-downloads-stats' ) . Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_Item_EvtLblEnable' ), Ui::AdminHelpBtnModeChkRad ), $idItems . '/' . $itemKey . '/' . $fldId, Gen::GetArrField( $item, $fldId, false, '/' ), true ) ) ) );
									}

									{
										$fldId = 'dataUrlPath';
										echo( Ui::Tag( 'tr', Ui::Tag( 'td', Ui::Tag( 'label', esc_html_x( 'PathLbl', 'admin.Settings_Items', 'seraphinite-downloads-stats' ) . Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_Item_Path' ) ) ) . Ui::TextBox( $idItems . '/' . $itemKey . '/' . $fldId, Gen::GetArrField( $item, $fldId, '', '/' ), array( 'placeholder' => 'Data URL relative path', 'class' => 'ctlMaxSizeX' ), true ) ) ) );
									}

									echo( Ui::TagOpen( 'tr' ) . Ui::TagOpen( 'td' ) );
									{
										$fldId = 'filesExtsIncl';
										echo( Ui::Tag( 'label', esc_html_x( 'FilesExtsInclLbl', 'admin.Settings_Items', 'seraphinite-downloads-stats' ) . Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_Item_FilesExtsIncl' ) ) ) );
										_SettOutputTokensEditor( $fldId, Gen::GetArrField( $item, $fldId, array(), '/' ), _x( 'FilesExtsInclEditPh', 'admin.Settings_Items', 'seraphinite-downloads-stats' ), $idItems . '/' . $itemKey );
									}
									echo( Ui::TagClose( 'td' ) . Ui::TagClose( 'tr' ) );
								}
								echo( Ui::SettBlock_ItemSubTbl_End() );

								return( ob_get_clean() );
							},

							function( $cbArgs, $attrs )
							{
								Gen::SetArrField( $attrs, 'class.+', 'ctlSpaceVAfter' );
								return( Ui::Tag( 'div', Ui::Tag( 'label', Ui::ItemsList_NoItemsContent() ), $attrs ) );
							},

							get_defined_vars(), array( 'class' => 'ctlMaxSizeX' )
						) );

                        echo( Ui::Tag( 'p', sprintf( esc_html_x( 'NoteStr_%1$s', 'admin.Settings_Items', 'seraphinite-downloads-stats' ), get_home_url( null, '/' ) ), array( 'class' => 'description ctlSpaceVBefore' ) ) );
					}
					echo( Ui::SettBlock_Item_End() );

					echo( Ui::SettBlock_Item_Begin( esc_html_x( 'Lbl', 'admin.Settings_EvLbl', 'seraphinite-downloads-stats' ) . Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_EvLbl' ) ) ) );
					{
						echo( Ui::SettBlock_ItemSubTbl_Begin() );

?>

							<tr>
								<td>
									<?php $fldId = 'evtLblEnable'; echo( Ui::CheckBox( esc_html_x( 'EnableChk', 'admin.Settings_EvLbl', 'seraphinite-downloads-stats' ), $fldId, Gen::GetArrField( $sett, $fldId, false, '/' ), true ) ); ?>
								</td>
							</tr>
							
							<tr>
								<td style="vertical-align:middle;"><?php echo( esc_html_x( 'SepLbl', 'admin.Settings_EvLbl', 'seraphinite-downloads-stats' ) ); ?></td>
								<td><?php $fldId = 'evtLblPrmSep'; $evtLblPrmSep = Gen::GetArrField( $sett, $fldId, ',', '/' ); echo( Ui::TextBox( $fldId, $evtLblPrmSep, array( 'size' => 1 ), true ) ); ?></td>
							</tr>
							
							<tr>
								<td style="vertical-align:middle;"><?php echo( esc_html_x( 'DelimLbl', 'admin.Settings_EvLbl', 'seraphinite-downloads-stats' ) ); ?></td>
								<td><?php $fldId = 'evtLblPrmValSep'; $evtLblPrmValSep = Gen::GetArrField( $sett, $fldId, ':', '/' ); echo( Ui::TextBox( $fldId, $evtLblPrmValSep, array( 'size' => 1 ), true ) ); ?></td>
							</tr>

						<?php

						echo( Ui::SettBlock_ItemSubTbl_End() );

                        echo( Ui::Tag( 'p', sprintf( esc_html_x( 'ExampleStr_%1$s%2$s%3$s', 'admin.Settings_EvLbl', 'seraphinite-downloads-stats' ), $evtLblPrmValSep, $evtLblPrmSep, $evtLblPrmValSep ), array( 'class' => 'description' ) ) );
					}
					echo( Ui::SettBlock_Item_End() );

					echo( Ui::SettBlock_Item_Begin( esc_html_x( 'Lbl', 'admin.Settings_SendMode', 'seraphinite-downloads-stats' ) . Ui::AdminHelpBtn( Plugin::RmtCfgFld_GetLoc( $rmtCfg, 'Help.Settings_SendMode' ) ) ) );
					{
						echo( Ui::SettBlock_ItemSubTbl_Begin() );
						{
							$intervals = _GetScheduleIntervals();

							$fldId2 = 'evtSendSchedInterval'; $fld2Val = Gen::GetArrField( $sett, $fldId2, SCHEDULEINTERVALS_DEFVAL, '/' );

							echo( Ui::Tag( 'tr', Ui::Tag( 'td',
								Ui::Label( sprintf( Ui::EscHtml( _nx( 'SchdEvery_%1$s', 'SchdEvery_%1$s', $intervals[ $fld2Val ][ 'displayPluralVal' ], 'admin.Settings_SendMode', 'seraphinite-downloads-stats' ), true ), Ui::ComboBox( $fldId2, array_map( function( $e ) { return( $e[ 'display' ] ); }, $intervals ), $fld2Val, true ) ) ),
								array( 'style' => array( 'vertical-align' => 'middle' ) ) ) ) );
						}
						echo( Ui::SettBlock_ItemSubTbl_End() );

					}
					echo( Ui::SettBlock_Item_End() );
				}
				echo( Ui::SettBlock_End() );
			},
			get_defined_vars()
		);

		do_action( 'seraph_dlstat_settings_display', $sett );
	}

	{
		$htmlContent = Plugin::GetSettingsLicenseContent();
		if( !empty( $htmlContent ) )
			Ui::PostBoxes_MetaboxAdd( 'license', Plugin::GetSettingsLicenseTitle(), true, function( $callbacks_args, $box ) { echo( $box[ 'args' ][ 'c' ] ); }, array( 'c' => $htmlContent ), 'normal' );

		$htmlContent = Plugin::GetAdvertProductsContent( 'advertProducts' );
		if( !empty( $htmlContent ) )
			Ui::PostBoxes_MetaboxAdd( 'advertProducts', Plugin::GetAdvertProductsTitle(), false, function( $callbacks_args, $box ) { echo( $box[ 'args' ][ 'c' ] ); }, array( 'c' => $htmlContent ), 'normal' );
	}

	{
		$htmlContent = Plugin::GetRateItContent( 'rateIt', Plugin::DisplayContent_SmallBlock );
		if( !empty( $htmlContent ) )
			Ui::PostBoxes_MetaboxAdd( 'rateIt', Plugin::GetRateItTitle(), false, function( $callbacks_args, $box ) { echo( $box[ 'args' ][ 'c' ] ); }, array( 'c' => $htmlContent ), 'side' );

		$htmlContent = Plugin::SwitchToExt( Plugin::DisplayContent_SmallBlock );
		if( !empty( $htmlContent ) )
			Ui::PostBoxes_MetaboxAdd( 'switchToExt', Plugin::GetSwitchToExtTitle(), false, function( $callbacks_args, $box ) { echo( $box[ 'args' ][ 'c' ] ); }, array( 'c' => $htmlContent ), 'side' );

		$htmlContent = Plugin::GetLockedFeatureLicenseContent( Plugin::DisplayContent_SmallBlock );
		if( !empty( $htmlContent ) )
			Ui::PostBoxes_MetaboxAdd( 'switchToFull', Plugin::GetSwitchToFullTitle(), false, function( $callbacks_args, $box ) { echo( $box[ 'args' ][ 'c' ] ); }, array( 'c' => $htmlContent ), 'side' );

		Ui::PostBoxes_MetaboxAdd( 'about', Plugin::GetAboutPluginTitle(), false, function( $callbacks_args, $box ) { echo( Plugin::GetAboutPluginContent() ); }, null, 'side' );
		Ui::PostBoxes_MetaboxAdd( 'aboutVendor', Plugin::GetAboutVendorTitle(), false, function( $callbacks_args, $box ) { echo( Plugin::GetAboutVendorContent() ); }, null, 'side' );
	}

	Ui::PostBoxes( Plugin::GetSettingsTitle(), array( 'body' => array( 'nosort' => false ), 'normal' => array(), 'side' => array( 'nosort' => true ) ),
		array(
			'bodyContentBegin' => function( $callbacks_args )
			{
				extract( $callbacks_args );

				echo( Ui::TagOpen( 'form', array( 'method' => 'post' ) ) );
			},

			'bodyContentEnd' => function( $callbacks_args )
			{
				extract( $callbacks_args );

				Ui::PostBoxes_BottomGroupPanel(
					function( $callbacks_args )
					{
						echo( Plugin::Sett_SaveBtn( 'seraph_dlstat_saveSettings' ) );
					}
				);

				echo( Ui::TagClose( 'form' ) );
			}
		),
		get_defined_vars()
	);
}

function _OnSaveSettings( $args )
{
	$sett = array();

	{ $fldId = 'evtLblEnable';			Gen::SetArrField( $sett, $fldId, isset( $args[ $fldId ] ), '/' ); }
	{ $fldId = 'evtLblPrmSep';			Gen::SetArrField( $sett, $fldId, $args[ $fldId ], '/' ); }
	{ $fldId = 'evtLblPrmValSep';		Gen::SetArrField( $sett, $fldId, $args[ $fldId ], '/' ); }
	{ $fldId = 'evtSendSchedInterval';	Gen::SetArrField( $sett, $fldId, intval( $args[ $fldId ] ), '/' ); }

	{
		$fldId = 'items';
		Gen::SetArrField( $sett, $fldId, Ui::ItemsList_GetSaveItems( $fldId, '/', $args,
			function( $cbArgs, $idItems, $itemKey, $item, $args )
			{
				$item = array();

				{ $fldId = 'enable';		Gen::SetArrField( $item, $fldId, isset( $args[ $idItems . '/' . $itemKey . '/' . $fldId ] ), '/' ); }
				{ $fldId = 'evtLblEnable';	Gen::SetArrField( $item, $fldId, isset( $args[ $idItems . '/' . $itemKey . '/' . $fldId ] ), '/' ); }
				{ $fldId = 'dataUrlPath';	Gen::SetArrField( $item, $fldId, trim( Gen::ToUnixSlashes( wp_unslash( trim( $args[ $idItems . '/' . $itemKey . '/' . $fldId ] ) ) ), '/' ), '/' ); }
				{ $fldId = 'filesExtsIncl';	Gen::SetArrField( $item, $fldId, Ui::TokensList_GetVal( (isset($args[ $idItems . '/' . $itemKey . '/' . $fldId ])?$args[ $idItems . '/' . $itemKey . '/' . $fldId ]:null) ), '/' ); }

				return( $item );
			}
		), '/' );
	}

	$hr = Gen::S_OK;
	$hr = Gen::HrAccom( $hr, apply_filters_ref_array( 'seraph_dlstat_onSettingsSave', array( Gen::S_FALSE, $args, &$sett ) ) );

	$hr = Gen::HrAccom( $hr, Plugin::SettSet( $sett ) );

	return( $hr );
}

