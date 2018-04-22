<?php
	$title = drupal_set_title($title ? $title : $file->filename);
	$enlargeDisabled = _eatlas_media_gallery_fixes_get_file_attribute_value($file, 'field_restricted_access', 'value');
	$source = _eatlas_media_gallery_fixes_get_file_attribute_value($file, 'field_source', 'value');
	$photographers = _eatlas_media_gallery_fixes_get_file_attribute_values($file, 'field_photographers', 'nid');
	$freeTextPhotographers = _eatlas_media_gallery_fixes_get_file_attribute_value($file, 'field_custom_photographers', 'value');
	//file_put_contents('/tmp/media-gallery-fixes_media-item-details-tpl_output.txt', print_r($file, true));
?>
<div class="media-gallery-detail-wrapper<?php print $enlargeDisabled ? ' private-detail-wrapper' : ''; ?>">
	<div class="media-gallery-detail">
		<!-- Navigation bar for media_gallery module -->
		<?php if ($module === 'media_gallery') { ?>
			<div class="media-gallery-navigation">
				<span class="media-gallery-back-link">
					<?php print l(t('« Back to gallery'), 'node/' . $gallery_node->nid); ?>
				</span>
				<span class="media-gallery-pager">
					<span class="media-gallery-image-count">
						<?php print t("Item @current of @total", array('@current' => $i_current + 1, '@total' => $num_items)); ?>
					</span>
					<span class="media-gallery-controls">
						<?php
							print $previous_link;
							print (!empty($previous_link) && !empty($next_link) ? ' | ' : '');
							print $next_link;
						?>
					</span>
				</span>
			</div>
		<?php } ?>

		<div style="text-align: center">
			<!-- For a list of all style names: http://<SERVER>/?q=admin/config/media/file-types/manage/image/display -->
			<!-- For file with all metadata: print render(file_view($file, 'm_very_large')); -->
			<?php
				$preview = $file;
				$previewValues = field_get_items('file', $file, 'field_preview');
				if ($previewValues) {
					$preview = file_load($previewValues[0]['fid']);
				}
				print render(file_view_file($preview, 'm_very_large'));
			?>
		</div>

		<div class="media-gallery-detail-info">
			<!-- Possible 'null' value for license:
				NULL => No value defined in DB
				'nothing' => The image has never been edited (that one is just silly)
				'none' => Image saved with license: "None (all rights reserved)" -->
			<?php print (!$license || $license === 'nothing' || $license === 'none' ? '' : $download_link); ?>
			<?php print theme('eatlas_media_gallery_fixes_license', array('file' => $file, 'color' => 'dark')); ?>
			<div class="media-gallery-notes">
				<?php print $notes ?>
			</div>
		</div>
		<?php if ($source) { ?>
			<div class="media-gallery-source">
				<label>Source: </label><?php print $source ?>
			</div>
		<?php } ?>

		<?php
		// or other EXIF fields that will go in that box
		if ($photographers || $freeTextPhotographers) { ?>
			<div class="media-gallery-image-info">
				<?php if ($photographers || $freeTextPhotographers) {
					// Get the photographers field label
					$photographers_field_info = field_info_instance('file', 'field_photographers', $file->type);
					if ($photographers_field_info) {
						$photographers_field_label = $photographers_field_info['label'];

						$photographerStr = '';
						$nbPhotographers = 0;
						if ($photographers) {
							foreach ($photographers as $photographerId) {
								$photographer = node_load($photographerId);
								if ($photographerStr) {
									$photographerStr .= ', ';
								}
								$photographerStr .= _eatlas_media_gallery_fixes_get_entity_markup($photographer);
								$nbPhotographers++;
							}
						}
						if ($freeTextPhotographers) {
							if ($photographerStr) {
								$photographerStr .= ', ';
							}
							$photographerStr .= $freeTextPhotographers;
							// Count the number of "," in the freeTextPhotographers (hopefully that correspond to the number of persons in it)
							$nbPhotographers += substr_count($freeTextPhotographers, ',') + 1;
						}
						if ($photographerStr) { ?>
							<div class="photographer">
								<?php print "$photographers_field_label: $photographerStr"; ?>
							</div>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			</div>
		<?php } ?>

		<?php if ($description) { ?>
			<div class="media-gallery-description">
				<?php print $description ?>
			</div>
		<?php } ?>

		<?php
		$projectsId = _eatlas_media_gallery_fixes_get_file_attribute_values($file, 'field_project', 'nid');
		if ($projectsId) {
			// Get the project field label
			$project_field_info = field_info_instance('file', 'field_project', $file->type);
			if ($project_field_info) {
				$project_field_label = $project_field_info['label'];

				print '<div class="media-projects">';
				print $project_field_label . '<ul>';
				foreach ($projectsId as $projectId) {
					$project = node_load($projectId);
					if ($project && node_access("view", $project)) {
						$projectTitle = $project->title;
						$projectUrl = url('node/' . $project->nid);
						print '<li><a href="' . $projectUrl . '">' . $projectTitle . '</a></li>';
					}
				}
				print '</ul></div>';
			}
		}

		$relatedContentsId = _eatlas_media_gallery_fixes_get_file_attribute_values($file, 'field_related_content', 'nid');
		if ($relatedContentsId) {
			// Get the related content field label
			$relatedContent_field_info = field_info_instance('file', 'field_related_content', $file->type);
			if ($relatedContent_field_info) {
				$relatedContent_field_label = $relatedContent_field_info['label'];

				print '<div class="media-related-content">';
				print $relatedContent_field_label . '<ul>';
				foreach ($relatedContentsId as $relatedContentId) {
					$relatedContent = node_load($relatedContentId);
					if ($relatedContent && node_access("view", $relatedContent)) {
						$relatedContentTitle = $relatedContent->title;
						$relatedContentUrl = url('node/' . $relatedContent->nid);
						print '<li><a href="' . $relatedContentUrl . '">' . $relatedContentTitle . '</a></li>';
					}
				}
				print '</ul></div>';
			}
		}

		$galleriesId = _eatlas_media_gallery_fixes_get_file_attribute_values($file, 'field_galleries', 'nid');
		if ($galleriesId) {
			// Get the gallery field label
			$gallery_field_info = field_info_instance('file', 'field_galleries', $file->type);
			if ($gallery_field_info) {
				$gallery_field_label = $gallery_field_info['label'];

				print '<div class="media-galleries">';
				print $gallery_field_label . '<ul>';
				foreach ($galleriesId as $galleryId) {
					$gallery = node_load($galleryId);
					if ($gallery && node_access("view", $gallery)) {
						$galleryTitle = $gallery->title;
						$galleryUrl = url('node/' . $gallery->nid);
						print '<li><a href="' . $galleryUrl . '">' . $galleryTitle . '</a></li>';
					}
				}
				print '</ul></div>';
			}
		}
		?>

		<!-- Navigation bar for media_gallery module -->
		<?php if ($module === 'media_gallery') { ?>
			<div class="media-gallery-navigation">
				<span class="media-gallery-back-link">
					<?php print l(t('« Back to gallery'), 'node/' . $gallery_node->nid); ?>
				</span>
				<span class="media-gallery-pager">
					<span class="media-gallery-image-count">
						<?php print t("Item @current of @total", array('@current' => $i_current + 1, '@total' => $num_items)); ?>
					</span>
					<span class="media-gallery-controls">
						<?php
							print $previous_link;
							print (!empty($previous_link) && !empty($next_link) ? ' | ' : '');
							print $next_link;
						?>
					</span>
				</span>
			</div>
		<?php } ?>
	</div>
</div>
