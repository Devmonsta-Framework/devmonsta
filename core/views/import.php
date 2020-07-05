<?php

/**
 * Hook for adding the custom plugin page header
 */
do_action( 'devm/plugin_page_header' );
?>

<div class="about-wrap wrap dms-container">

	<?php ob_start();?>
	<h1 class="dms--importer-title"><span class="dms--importer-title__icon dms dms-cog-icon1"></span><?php esc_html_e( 'Demo Install DMS', 'dms' );?></h1>
	<?php
$plugin_title = ob_get_clean();

// Display the plugin title (can be replaced with custom title text through the filter below).
echo wp_kses_post( apply_filters( 'devm/plugin_page_title', $plugin_title ) );

// Display warrning if PHP safe mode is enabled, since we wont be able to change the max_execution_time.
if ( ini_get( 'safe_mode' ) ) {
    printf(
        esc_html__( '%sWarning: your server is using %sPHP safe mode%s. This means that you might experience server timeout errors.%s', 'dms' ),
        '<div class="notice  notice-warning  is-dismissible"><p>',
        '<strong>',
        '</strong>',
        '</p></div>'
    );
}

// Start output buffer for displaying the plugin intro text.
ob_start();
?>

	<div class="dms__intro-notice  notice  notice-warning  is-dismissible">
		<p><?php esc_html_e( 'Before you begin, make sure all the required plugins are activated.', 'dms' );?></p>
	</div>

	<?php
$plugin_intro_text = ob_get_clean();

// Display the plugin intro text (can be replaced with custom text through the filter below).
echo wp_kses_post( apply_filters( 'devm/plugin_intro_text', $plugin_intro_text ) );
?>
	<?php

		$demo_files = devm_import_files();
		devm_print( count( $demo_files ) );

	?>
	<?php if ( count( $demo_files ) < 1 ): ?>
		<div class="notice  notice-info  is-dismissible">
			<p><?php esc_html_e( 'There are no predefined import files available in this theme. Please upload the import files manually!', 'dms' );?></p>
		</div>
	<?php endif;?>

	<!-- Show demo import options -->


	<div class="dms--demo-preview-list">
		<?php

			foreach ( $demo_files as $single_demo_file ) {
				$nonce = wp_create_nonce( "dms_demo_import_nonce" );
				$link  = admin_url( 'admin-ajax.php?action=dms_import_demo&nonce=' . $nonce );
				?>
						<div class="card dms--demo-preview-list__item">
							<div class="dms--demo-preivew-inner">
								<div class="dms--demo-preview-list__item--thumb" style="background-image:url(<?php echo esc_url( $single_demo_file["import_preview_image_url"] ); ?>)"></div>
								<div class="card-body">
									<h3 class="card-title"><?php echo esc_html( $single_demo_file["import_title"] ); ?></h3>

									<div class="dms-preview-btn-list">
										<a href="#" class="attr-btn-primary dms-preview-btn"><span class="dms-preview-btn-icon dms dms-laptop"></span><?php esc_html_e( 'Preview', 'dms' );?></a>

										<button data-required-plugin='<?php echo json_encode( $single_demo_file['required_plugin'] ); ?>' class="dms-special-preview-btn dms_import_btn btn attr-btn-primary" data-nonce="<?php echo esc_attr( $nonce ); ?>" data-name="<?php echo esc_attr( $single_demo_file["import_title"] ); ?>" data-xml_link="<?php echo esc_url( $single_demo_file['import_file_url'] ); ?>"><span class="dms-special-preview-btn-icon dms dms-download1"></span><?php echo esc_html__( 'Import', 'dms' ); ?></button>
									</div>
								</div>
							</div>
						</div>
					<?php
			}

			?>
	</div>
	<div class="dms__response  js-dms-ajax-response"></div>
</div>
<!-- Modal -->

<div class="attr-modal attr-fade" id="dms-importMmodal" data-backdrop="true" data-keyboard="false" tabindex="-1" role="attr-dialog" aria-labelledby="dms-importMmodalLabel" aria-hidden="true">
	<div class="attr-modal-dialog attr-modal-dialog-centered" role="attr-document">
		<div class="attr-modal-content">
			<div class="attr-modal-body dms-import-flip-next dms-modal-main-content" data-step="1">

				<div class="dms-single-content">
					<div class="dms-single-content--preview-img"><img src="<?php echo devm_get_framework_directory_uri() . '/static/img/import-preview-1.png'; ?>" alt=""></div>
					<div class="dms-single-content--preview-img"><img src="<?php echo devm_get_framework_directory_uri() . '/static/img/import-preview-2.png'; ?>" alt=""></div>
					<div class="dms-single-content--preview-img"><img src="<?php echo devm_get_framework_directory_uri() . '/static/img/import-preview-3.png'; ?>" alt=""></div>
					<div class="dms-single-content--preview-img"><img src="<?php echo devm_get_framework_directory_uri() . '/static/img/import-preview-4.png'; ?>" alt=""></div>
					<div class="dms-single-content--preview-img"><img src="<?php echo devm_get_framework_directory_uri() . '/static/img/import-preview-5.png'; ?>" alt=""></div>
				</div>

				<div class="dms-single-content">
					<div class="dms-importer-data">
						<div class="dms-single-importer welcome" data-step="welcome">
							<h1 class="dms-importer-data--welcome-title"><?php esc_html_e( 'Welcome Back!', 'dms' );?></h1>
							<div class="dms-importer-data--welcome-text">
								<p><?php esc_html_e( 'For better and faster result, It’s recommended to install the demo on a clean WordPress website.', 'dms' );?></p>
							</div>
						</div>

						<div class="dms-single-importer erase" data-step="erase">
							<h1 class="dms-importer-data--welcome-title"><?php esc_html_e( 'Erase Previous Data', 'dms' );?></h1>
							<div class="dms-importer-data--welcome-text">
								<p><?php esc_html_e( 'All previous data will be erased. There is no UNDO. No backup will be generated.', 'dms' );?></p>
							</div>
							<div class="dms-importer-additional-data">
								<div class="dms-importer-checkbox">
									<input class="form-check-input" type="checkbox" value="" id="dms_delete_data_confirm">
									<label for="dms_delete_data_confirm" class="dms-importer-check-label"><?php echo esc_html__( 'I do confirm.', 'dms' ); ?></label>
								</div>
							</div>
						</div>

						<div class="dms-single-importer plugin_install" data-step="plugin_install">
							<h1 class="dms-importer-data--welcome-title"><?php esc_html_e( 'Required Plugins', 'dms' );?></h1>
							<div class="dms-importer-plugin-list"></div>
							<div class="dms-importer-additional-data">

								<div class="attr-progress dms-progress-bar">
									<div class="attr-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
						</div>

						<div class="dms-single-importer content_import" data-step="content_import">
							<h2 class="dms-importer-data--progress-msg"><?php esc_html_e( 'Demo Content is in Progress', 'dms' );?></h2>
							<h1 class="dms-importer-data--welcome-title"><?php esc_html_e( 'We are ready to import.', 'dms' );?></h1>
							<div class="dms-importer-data--welcome-title dms-loading">Importing</div>
							<div class="dms-importer-data--welcome-text">
								<p><?php esc_html_e( 'This process may take 05 to 10 minutes to complete. Please do not close or refresh this page.', 'dms' );?></p>
							</div>
						</div>

						<div class="dms-single-importer last_step" data-step="last_step">
							<h1 class="dms-importer-data--welcome-title"><?php esc_html_e( 'Welcome', 'dms' )?></h1>
							<div class="dms-importer-data--welcome-text">
								<p><?php esc_html_e( 'Demo has been successfully imported!', 'dms' )?></p>
							</div>
						</div>

					</div>
					<div class="dms-importer-buttons">
						<div class="dms-importer-final-buttons">
							<button type="button" class="dms-btn dms-close-btn" data-dismiss="modal"><?php esc_html_e( 'Close', 'dms' );?></button>
							<a target="_blank" class="dms-btn dms-special-btn" href="<?php echo esc_url( get_home_url() ); ?>"><?php echo esc_html__( 'Preview', 'dms' ); ?></a>
							<a target="_blank" class="dms-btn dms-special-btn" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php echo esc_html__( 'Customise', 'dms' ); ?></a>
						</div>

						<div class="dms-importer-normal-buttons">
							<button type="button" class="dms-btn dms-close-btn" data-dismiss="modal"><?php esc_html_e( 'Close', 'dms' );?></button>
							<button type="button" class="dms-btn dms-skip-btn">
								<div class="attr-spinner-border" role="status">
									<span class="attr-sr-only"><?php esc_html_e( 'Loading...', 'dms' );?></span>
								</div>
								<?php echo esc_html__( 'Skip', 'dms' ); ?>
							</button>
							<button type="button" class="dms-btn dms-continue-btn">
								<div class="attr-spinner-border" role="status">
									<span class="attr-sr-only"><?php esc_html_e( 'Loading...', 'dms' );?></span>
								</div>
								<?php echo esc_html__( 'Continue', 'dms' ); ?>
							</button>
						</div>

					</div>
				</div>

			</div>
		</div>
		<span data-dismiss="modal" class="dms-close-btn dms-importer-close-modal dms dms-cancel"></span>
	</div>



	<?php
/**
 * Hook for adding the custom admin page footer
 */
do_action( 'devm/plugin_page_footer' );
?>