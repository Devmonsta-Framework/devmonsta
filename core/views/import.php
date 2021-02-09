<?php
/**
 * Hook for adding the custom plugin page header
 */
do_action( 'devm/plugin_page_header' );
?>

<div class="about-wrap wrap devm-container">
	<?php ob_start();?>
	<h1 class="devm--importer-title"><span class="devm--importer-title__icon devm devm-cog-icon1"></span><?php esc_html_e( 'Devmonsta Demo Install', 'devmonsta' );?></h1>
	<?php
	$plugin_title = ob_get_clean();

	// Display the plugin title (can be replaced with custom title text through the filter below).
	echo wp_kses_post( apply_filters( 'devm/plugin_page_title', $plugin_title ) );

	// Display warrning if PHP safe mode is enabled, since we wont be able to change the max_execution_time.
	if ( ini_get( 'safe_mode' ) ) {
		printf(
			esc_html__( '%sWarning: your server is using %sPHP safe mode%s. This means that you might experience server timeout errors.%s', 'devmonsta' ),
			'<div class="notice  notice-warning  is-dismissible"><p>',
			'<strong>',
			'</strong>',
			'</p></div>'
		);
	}

	// Start output buffer for displaying the plugin intro text.
	ob_start();
	?>
	<div class="devm__intro-notice  notice  notice-warning  is-dismissible">
		<p><?php esc_html_e( 'Before you begin, make sure all the required plugins are activated.', 'devmonsta' );?></p>
	</div>
	<?php
	$plugin_intro_text = ob_get_clean();

	// Display the plugin intro text (can be replaced with custom text through the filter below).
	echo wp_kses_post( apply_filters( 'devm/plugin_intro_text', $plugin_intro_text ) );
	$demo_files = devm_import_files();

	if ( count( $demo_files ) < 1 ): 
		?>
		<div class="notice  notice-info  is-dismissible">
			<p><?php esc_html_e( 'There are no predefined import files available in this theme. Please upload the import files manually!', 'devmonsta' );?></p>
		</div>
		<?php 
	endif;
	?>

	<!-- Show demo import options -->
	<div class="devm--demo-preview-list">
		<?php
		foreach ( $demo_files as $single_demo_file ) {
			$nonce = wp_create_nonce( "devm_demo_import_nonce" );
			$link  = admin_url( 'admin-ajax.php?action=devm_import_demo&nonce=' . $nonce );
			?>
			<div class="card devm--demo-preview-list__item">
				<div class="devm--demo-preivew-inner">
					<div class="devm--demo-preview-list__item--thumb" style="background-image:url(<?php echo esc_url( $single_demo_file["import_preview_image_url"] ); ?>)"></div>
					<div class="card-body">
						<h3 class="card-title"><?php echo esc_html( $single_demo_file["import_title"] ); ?></h3>

						<div class="devm-preview-btn-list">
							<a href="#" class="attr-btn-primary devm-preview-btn"><span class="devm-preview-btn-icon devm devm-laptop"></span><?php esc_html_e( 'Preview', 'devmonsta' );?></a>

							<button data-selected_demo='<?php echo json_encode( $single_demo_file ); ?>'
								data-required-plugin='<?php echo json_encode( $single_demo_file['required_plugin'] ); ?>'
								class="devm-special-preview-btn devm_import_btn btn attr-btn-primary"
								data-nonce="<?php echo esc_attr( $nonce ); ?>"
								data-name="<?php echo esc_attr( $single_demo_file["import_title"] ); ?>"
								data-xml_link="<?php echo esc_url( $single_demo_file['import_file_url'] ); ?>">
								<span class="devm-special-preview-btn-icon devm devm-download1"></span><?php echo esc_html__( 'Import', 'devmonsta' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

	?>
	</div>
	<div class="devm__response  js-devm-ajax-response"></div>
</div>
<!-- Modal -->

<div class="attr-modal attr-fade" id="devm-importMmodal" data-backdrop="true" data-keyboard="false" tabindex="-1" role="attr-dialog" aria-labelledby="devm-importMmodalLabel" aria-hidden="true">
	<div class="attr-modal-dialog attr-modal-dialog-centered" role="attr-document">
		<div class="attr-modal-content">
			<div class="attr-modal-body devm-import-flip-next devm-modal-main-content" data-step="1">
				<div class="devm-single-content">
					<div class="devm-single-content--preview-img"><img src="<?php echo devm_get_framework_directory_uri() . '/static/img/import-preview-1.png'; ?>" alt=""></div>
					<div class="devm-single-content--preview-img"><img src="<?php echo devm_get_framework_directory_uri() . '/static/img/import-preview-2.png'; ?>" alt=""></div>
					<div class="devm-single-content--preview-img"><img src="<?php echo devm_get_framework_directory_uri() . '/static/img/import-preview-3.png'; ?>" alt=""></div>
					<div class="devm-single-content--preview-img"><img src="<?php echo devm_get_framework_directory_uri() . '/static/img/import-preview-4.png'; ?>" alt=""></div>
					<div class="devm-single-content--preview-img"><img src="<?php echo devm_get_framework_directory_uri() . '/static/img/import-preview-5.png'; ?>" alt=""></div>
				</div>
				<div class="devm-single-content">
					<div class="devm-importer-data">
						<div class="devm-single-importer welcome" data-step="welcome">
							<h1 class="devm-importer-data--welcome-title"><?php esc_html_e( 'Welcome Back!', 'devmonsta' );?></h1>
							<div class="devm-importer-data--welcome-text">
								<p><?php esc_html_e( 'For better and faster result, It’s recommended to install the demo on a clean WordPress website.', 'devmonsta' );?></p>
							</div>
						</div>

						<div class="devm-single-importer erase" data-step="erase">
							<h1 class="devm-importer-data--welcome-title"><?php esc_html_e( 'Erase Previous Data', 'devmonsta' );?></h1>
							<div class="devm-importer-data--welcome-text">
								<p><?php esc_html_e( 'All previous data will be erased. There is no UNDO. No backup will be generated.', 'devmonsta' );?></p>
							</div>
							<div class="devm-importer-additional-data">
								<div class="devm-importer-checkbox">
									<input class="form-check-input" type="checkbox" value="" id="devm_delete_data_confirm">
									<label for="devm_delete_data_confirm" class="devm-importer-check-label"><?php echo esc_html__( 'I do confirm.', 'devmonsta' ); ?></label>
								</div>
							</div>
						</div>

						<div class="devm-single-importer plugin_install" data-step="plugin_install">
							<h1 class="devm-importer-data--welcome-title">
								<?php esc_html_e( 'Required Plugins', 'devmonsta' );?>
							</h1>
							<div class="devm-importer-plugin-list">
							</div>
							<div class="devm-importer-additional-data">
								<div class="attr-progress devm-progress-bar">
									<div class="attr-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
									</div>
								</div>
							</div>
						</div>

						<div class="devm-single-importer content_import" data-step="content_import">
							<h2 class="devm-importer-data--progress-msg"><?php esc_html_e( 'Demo Content is in Progress', 'devmonsta' );?></h2>
							<h1 class="devm-importer-data--welcome-title"><?php esc_html_e( 'We are ready to import.', 'devmonsta' );?></h1>
							<div class="devm-importer-data--welcome-title devm-loading">Importing</div>
							<div class="devm-importer-data--welcome-text">
								<p><?php esc_html_e( 'This process may take 05 to 10 minutes to complete. Please do not close or refresh this page.', 'devmonsta' );?></p>
							</div>
						</div>

						<div class="devm-single-importer last_step" data-step="last_step">
							<h1 class="devm-importer-data--welcome-title"><?php esc_html_e( 'Welcome', 'devmonsta' )?></h1>
							<div class="devm-importer-data--welcome-text">
								<p><?php esc_html_e( 'Demo has been successfully imported!', 'devmonsta' )?></p>
							</div>
						</div>
					</div>
					<div class="devm-importer-buttons">
						<div class="devm-importer-final-buttons">
							<button type="button" class="devm-btn devm-close-btn" data-dismiss="modal"><?php esc_html_e( 'Close', 'devmonsta' );?></button>
							<a target="_blank" class="devm-btn devm-special-btn" href="<?php echo esc_url( get_home_url() ); ?>"><?php echo esc_html__( 'Preview', 'devmonsta' ); ?></a>
							<a target="_blank" class="devm-btn devm-special-btn" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php echo esc_html__( 'Customise', 'devmonsta' ); ?></a>
						</div>

						<div class="devm-importer-normal-buttons">
							<button type="button" class="devm-btn devm-close-btn" data-dismiss="modal"><?php esc_html_e( 'Close', 'devmonsta' );?></button>
							<button type="button" class="devm-btn devm-skip-btn">
								<div class="attr-spinner-border" role="status">
									<span class="attr-sr-only"><?php esc_html_e( 'Loading...', 'devmonsta' );?></span>
								</div>
								<?php echo esc_html__( 'Skip', 'devmonsta' ); ?>
							</button>
							<button type="button" class="devm-btn devm-continue-btn">
								<div class="attr-spinner-border" role="status">
									<span class="attr-sr-only"><?php esc_html_e( 'Loading...', 'devmonsta' );?></span>
								</div>
								<?php echo esc_html__( 'Continue', 'devmonsta' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<span data-dismiss="modal" class="devm-close-btn devm-importer-close-modal devm devm-cancel"></span>
	</div>
</div>

<?php
/**
 * Hook for adding the custom admin page footer
 */
do_action( 'devm/plugin_page_footer' );
?>