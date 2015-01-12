<?php

class CCF_Field_Renderer {

	/**
	 * Placeholder method
	 *
	 * @since 6.0
	 */
	public function __construct() {}

	/**
	 * Get single-line-text field HTML, including any errors from the last form submission. if there is an
	 * error the field will remember it's last submitted value.
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function single_line_text( $field_id, $form_id ) {
		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$label = get_post_meta( $field_id, 'ccf_field_label', true );
		$value = get_post_meta( $field_id, 'ccf_field_value', true );
		$placeholder = get_post_meta( $field_id, 'ccf_field_placeholder', true );
		$required = get_post_meta( $field_id, 'ccf_field_required', true );
		$class_name = get_post_meta( $field_id, 'ccf_field_className', true );

		$errors = CCF_Form_Handler::factory()->get_errors( $form_id, $slug );
		$all_errors = CCF_Form_Handler::factory()->get_errors( $form_id );

		if ( ! empty( $all_errors ) ) {
			if ( apply_filters( 'ccf_show_last_field_value', true, $field_id ) ) {
				if ( ! empty( $_POST['ccf_field_' . $slug] ) ) {
					$post_value = $_POST['ccf_field_' . $slug];
				}
			}
		}

		ob_start();
		?>

		<div data-field-type="single-line-text" class="<?php if ( ! empty( $errors ) ) : ?>field-error<?php endif; ?> field <?php echo esc_attr( $slug ); ?> single-line-text field-<?php echo (int) $field_id; ?> <?php echo esc_attr( $class_name ); ?> <?php if ( ! empty( $required ) ) : ?>field-required<?php endif; ?>">
			<label for="ccf_field_<?php echo esc_attr( $slug ); ?>">
				<?php if ( ! empty( $required ) ) : ?><span class="required">*</span><?php endif; ?>
				<?php echo esc_html( $label ); ?>
			</label>
			<input class="<?php if ( ! empty( $errors ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> type="text" name="ccf_field_<?php echo esc_attr( $slug ); ?>" id="ccf_field_<?php echo esc_attr( $slug ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="<?php if ( ! empty( $post_value ) ) { echo esc_attr( $post_value ); } else { echo esc_attr( $value ); } ?>">
			<?php if ( ! empty( $errors ) ) : ?>
				<div class="error"><?php echo esc_html( $errors['required'] ); ?></div>
			<?php endif; ?>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get section header layout field HTML
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function section_header( $field_id, $form_id ) {
		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$heading = get_post_meta( $field_id, 'ccf_field_heading', true );
		$subheading = get_post_meta( $field_id, 'ccf_field_subheading', true );
		$class_name = get_post_meta( $field_id, 'ccf_field_className', true );

		ob_start();
		?>

		<div class="field skip-field <?php echo esc_attr( $slug ); ?> section-header field-<?php echo (int) $field_id; ?> <?php echo esc_attr( $class_name ); ?>">
			<div class="heading">
				<?php echo esc_html( $heading ); ?>
			</div>
			<div class="subheading">
				<?php echo esc_html( $subheading ); ?>
			</div>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get html layout field HTML
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function html( $field_id, $form_id ) {
		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$html = get_post_meta( $field_id, 'ccf_field_html', true );
		$class_name = get_post_meta( $field_id, 'ccf_field_className', true );

		ob_start();
		?>

		<div class="field skip-field <?php echo esc_attr( $slug ); ?> html field-<?php echo (int) $field_id; ?> <?php echo esc_attr( $class_name ); ?>">
			<?php echo wp_kses_post( $html ); ?>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get meta info for a given choice
	 *
	 * @param int $choice_id
	 * @since 6.0
	 * @return array
	 */
	private function get_choice( $choice_id ) {
		$choice = array(
			'value' => get_post_meta( $choice_id, 'ccf_choice_value', true ),
			'label' => get_post_meta( $choice_id, 'ccf_choice_label', true ),
			'selected' => get_post_meta( $choice_id, 'ccf_choice_selected', true ),
		);

		return $choice;
	}

	/**
	 * Get dropdown field HTML, including any errors from the last form submission. if there is an error the
	 * field will remember it's last submitted value.
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function dropdown( $field_id, $form_id ) {
		$choice_ids = get_post_meta( $field_id, 'ccf_attached_choices', true );

		$choices = array();
		$selected = 0;
		if ( ! empty( $choice_ids ) ) {
			foreach ( $choice_ids as $choice_id ) {
				$choices[$choice_id] = $this->get_choice( $choice_id );

				if ( ! empty( $choices[$choice_id]['selected'] ) ) {
					$selected++;
				}
			}
		}

		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$label = get_post_meta( $field_id, 'ccf_field_label', true );
		$class_name = get_post_meta( $field_id, 'ccf_field_className', true );
		$required = get_post_meta( $field_id, 'ccf_field_required', true );

		$errors = CCF_Form_Handler::factory()->get_errors( $form_id, $slug );
		$all_errors = CCF_Form_Handler::factory()->get_errors( $form_id );

		if ( ! empty( $all_errors ) ) {
			if ( apply_filters( 'ccf_show_last_field_value', true, $field_id ) ) {
				if ( ! empty( $_POST['ccf_field_' . $slug] ) ) {
					$post_value = $_POST['ccf_field_' . $slug];
				}
			}
		}

		ob_start();
		?>

		<div data-field-type="dropdown" class="<?php if ( ! empty( $errors ) ) : ?>field-error<?php endif; ?> field <?php echo esc_attr( $slug ); ?> dropdown field-<?php echo (int) $field_id; ?> <?php echo esc_attr( $class_name ); ?> <?php if ( ! empty( $required ) ) : ?>field-required<?php endif; ?>">
			<label for="ccf_field_<?php echo esc_attr( $slug ); ?>">
				<?php if ( ! empty( $required ) ) : ?><span class="required">*</span><?php endif; ?>
				<?php echo esc_html( $label ); ?>
			</label>
			<select class="<?php if ( ! empty( $errors ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> <?php if ( $selected > 1 ) : ?>multiple<?php endif; ?> name="ccf_field_<?php echo esc_attr( $slug ); ?>" id="ccf_field_<?php echo esc_attr( $slug ); ?>">
				<?php foreach ( $choices as $choice ) :
					$selected = '';
					if ( ! empty( $post_value ) ) {
						if ( $choice['value'] == $post_value ) {
							$selected = 'selected';
						}
					} else {
						if ( ! empty( $choice['selected'] ) ) {
							$selected = 'selected';
						}
					}
					?>
					<option <?php echo $selected; ?> value="<?php echo esc_attr( $choice['value'] ); ?>"><?php echo esc_html( $choice['label'] ); ?></option>
				<?php endforeach; ?>
			</select>

			<?php if ( CCF_Form_Handler::factory()->get_errors( $form_id, $slug ) ) : ?>
				<div class="error"><?php echo esc_html( CCF_Form_Handler::factory()->get_errors( $form_id, $slug )['required'] ); ?></div>
			<?php endif; ?>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get checkboxes field HTML, including any errors from the last form submission. if there is an error the
	 * field will remember it's last submitted value.
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function checkboxes( $field_id, $form_id ) {
		$choice_ids = get_post_meta( $field_id, 'ccf_attached_choices', true );

		$choices = array();
		$selected = 0;
		if ( ! empty( $choice_ids ) ) {
			foreach ( $choice_ids as $choice_id ) {
				$choices[$choice_id] = $this->get_choice( $choice_id );

				if ( ! empty( $choices[$choice_id]['selected'] ) ) {
					$selected++;
				}
			}
		}

		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$label = get_post_meta( $field_id, 'ccf_field_label', true );;
		$required = get_post_meta( $field_id, 'ccf_field_required', true );
		$class_name = get_post_meta( $field_id, 'ccf_field_className', true );

		$errors = CCF_Form_Handler::factory()->get_errors( $form_id, $slug );
		$all_errors = CCF_Form_Handler::factory()->get_errors( $form_id );

		if ( ! empty( $all_errors ) ) {
			if ( apply_filters( 'ccf_show_last_field_value', true, $field_id ) ) {
				if ( ! empty( $_POST['ccf_field_' . $slug] ) ) {
					$post_value = $_POST['ccf_field_' . $slug];
				}
			}
		}

		ob_start();
		?>

		<div data-field-type="checkboxes" class="<?php if ( ! empty( $errors ) ) : ?>field-error<?php endif; ?> field <?php echo esc_attr( $slug ); ?> checkboxes field-<?php echo (int) $field_id; ?> <?php echo esc_attr( $class_name ); ?> <?php if ( ! empty( $required ) ) : ?>field-required<?php endif; ?>">
			<label for="ccf_field_<?php echo esc_attr( $slug ); ?>">
				<?php if ( ! empty( $required ) ) : ?><span class="required">*</span><?php endif; ?>
				<?php echo esc_html( $label ); ?>
			</label>
			<?php foreach ( $choices as $choice ) :
				$checked = '';
				if ( ! empty( $post_value ) ) {
					if ( in_array( $choice['value'], $post_value ) ) {
						$checked = 'checked';
					}
				} else {
					if ( ! empty( $choice['selected'] ) ) {
						$checked = 'checked';
					}
				}
				?>
				<div class="choice">
					<input class="field-input" name="ccf_field_<?php echo esc_attr( $slug ); ?>[]" type="checkbox" <?php echo $checked; ?> value="<?php echo esc_attr( $choice['value'] ); ?>"> <span><?php echo esc_html( $choice['label'] ); ?></span>
				</div>
			<?php endforeach; ?>

			<?php if ( ! empty( $errors ) ) : ?>
				<div class="error"><?php echo esc_html( $errors['required'] ); ?></div>
			<?php endif; ?>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get radio field HTML, including any errors from the last form submission. if there is an error the
	 * field will remember it's last submitted value.
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function radio( $field_id, $form_id ) {
		$choice_ids = get_post_meta( $field_id, 'ccf_attached_choices', true );

		$choices = array();
		$selected = 0;
		if ( ! empty( $choice_ids ) ) {
			foreach ( $choice_ids as $choice_id ) {
				$choices[$choice_id] = $this->get_choice( $choice_id );

				if ( ! empty( $choices[$choice_id]['selected'] ) ) {
					$selected++;
				}
			}
		}

		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$label = get_post_meta( $field_id, 'ccf_field_label', true );;
		$required = get_post_meta( $field_id, 'ccf_field_required', true );
		$class_name = get_post_meta( $field_id, 'ccf_field_className', true );

		$errors = CCF_Form_Handler::factory()->get_errors( $form_id, $slug );
		$all_errors = CCF_Form_Handler::factory()->get_errors( $form_id );

		if ( ! empty( $all_errors ) ) {
			if ( apply_filters( 'ccf_show_last_field_value', true, $field_id ) ) {
				if ( ! empty( $_POST['ccf_field_' . $slug] ) ) {
					$post_value = $_POST['ccf_field_' . $slug];
				}
			}
		}

		ob_start();
		?>

		<div data-field-type="radio" class="<?php if ( ! empty( $errors ) ) : ?>field-error<?php endif; ?> field <?php echo esc_attr( $slug ); ?> radio field-<?php echo (int) $field_id; ?> <?php echo esc_attr( $class_name ); ?> <?php if ( ! empty( $required ) ) : ?>field-required<?php endif; ?>">
			<label for="ccf_field_<?php echo esc_attr( $slug ); ?>">
				<?php if ( ! empty( $required ) ) : ?><span class="required">*</span><?php endif; ?>
				<?php echo esc_html( $label ); ?>
			</label>
			<?php foreach ( $choices as $choice ) :
				$checked = '';
				if ( ! empty( $post_value ) ) {
					if ( $choice['value'] == $post_value ) {
						$checked = 'checked';
					}
				} else {
					if ( ! empty( $choice['selected'] ) ) {
						$checked = 'checked';
					}
				}
				?>
				<div class="choice">
					<input class="field-input" name="ccf_field_<?php echo esc_attr( $slug ); ?>" type="radio" <?php echo $checked; ?> value="<?php echo esc_attr( $choice['value'] ); ?>"> <span><?php echo esc_html( $choice['label'] ); ?></span>
				</div>
			<?php endforeach; ?>

			<?php if ( ! empty( $errors ) ) : ?>
				<div class="error"><?php echo esc_html( $errors['required'] ); ?></div>
			<?php endif; ?>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get address field HTML, including any errors from the last form submission. if there is an error the
	 * field will remember it's last submitted value.
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function address( $field_id, $form_id ) {
		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$label = get_post_meta( $field_id, 'ccf_field_label', true );
		$address_type = get_post_meta( $field_id, 'ccf_field_addressType', true );
		$required = get_post_meta( $field_id, 'ccf_field_required', true );
		$class_name = get_post_meta( $field_id, 'ccf_field_className', true );

		$errors = CCF_Form_Handler::factory()->get_errors( $form_id, $slug );
		$all_errors = CCF_Form_Handler::factory()->get_errors( $form_id );

		if ( ! empty( $all_errors ) ) {
			if ( apply_filters( 'ccf_show_last_field_value', true, $field_id ) ) {
				if ( ! empty( $_POST['ccf_field_' . $slug]['street'] ) ) {
					$street_post_value = $_POST['ccf_field_' . $slug]['street'];
				}

				if ( ! empty( $_POST['ccf_field_' . $slug]['line_two'] ) ) {
					$line_two_post_value = $_POST['ccf_field_' . $slug]['line_two'];
				}

				if ( ! empty( $_POST['ccf_field_' . $slug]['line_two'] ) ) {
					$line_two_post_value = $_POST['ccf_field_' . $slug]['line_two'];
				}

				if ( ! empty( $_POST['ccf_field_' . $slug]['city'] ) ) {
					$city_post_value = $_POST['ccf_field_' . $slug]['city'];
				}

				if ( ! empty( $_POST['ccf_field_' . $slug]['state'] ) ) {
					$state_post_value = $_POST['ccf_field_' . $slug]['state'];
				}

				if ( ! empty( $_POST['ccf_field_' . $slug]['country'] ) ) {
					$country_post_value = $_POST['ccf_field_' . $slug]['country'];
				}

				if ( ! empty( $_POST['ccf_field_' . $slug]['zipcode'] ) ) {
					$zipcode_post_value = $_POST['ccf_field_' . $slug]['zipcode'];
				}
			}
		}

		ob_start();
		?>

		<div data-field-type="address" class="<?php if ( ! empty( $errors ) ) : ?>field-error<?php endif; ?> field <?php echo esc_attr( $slug ); ?> address field-<?php echo (int) $field_id; ?> <?php echo esc_attr( $class_name ); ?> <?php if ( ! empty( $required ) ) : ?>field-required<?php endif; ?>">
			<label for="ccf_field_<?php echo esc_attr( $slug ); ?>">
				<?php if ( ! empty( $required ) ) : ?><span class="required">*</span><?php endif; ?>
				<?php echo esc_html( $label ); ?>
			</label>
			<div class="full">
				<input value="<?php if ( ! empty( $street_post_value ) ) echo esc_attr( $street_post_value ); ?>" class="<?php if ( ! empty( $errors['street_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> id="ccf_field_<?php echo esc_attr( $slug ); ?>-street" type="text" name="ccf_field_<?php echo esc_attr( $slug ); ?>[street]">
				<?php if ( ! empty( $errors['street_required'] ) ) : ?>
					<div class="error"><?php echo esc_html( $errors['street_required'] ); ?></div>
				<?php endif; ?>
				<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-street" class="sub-label">Street Address</label>
			</div>
			<div class="full">
				<input value="<?php if ( ! empty( $line_two_post_value ) ) echo esc_attr( $line_two_post_value ); ?>" class="<?php if ( ! empty( $errors['line_two_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> id="ccf_field_<?php echo esc_attr( $slug ); ?>-line_two" type="text" name="ccf_field_<?php echo esc_attr( $slug ); ?>[line_two]">
				<?php if ( ! empty( $errors['line_two_required'] ) ) : ?>
					<div class="error"><?php echo esc_html( $errors['line_two_required'] ); ?></div>
				<?php endif; ?>
				<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-line_two" class="sub-label">Address Line 2</label>

			</div>
			<div class="left">
				<input value="<?php if ( ! empty( $city_post_value ) ) echo esc_attr( $city_post_value ); ?>" class="<?php if ( ! empty( $errors['city_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> type="text" name="ccf_field_<?php echo esc_attr( $slug ); ?>[city]" id="ccf_field_<?php echo esc_attr( $slug ); ?>-city">
				<?php if ( ! empty( $errors['city_required'] ) ) : ?>
					<div class="error"><?php echo esc_html( $errors['city_required'] ); ?></div>
				<?php endif; ?>
				<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-city" class="sub-label">City</label>

			</div>
			<?php if ( $address_type === 'us' ) { ?>
				<div class="right">
					<select class="<?php if ( ! empty( $errors['state_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> name="ccf_field_<?php echo esc_attr( $slug ); ?>[state]" id="ccf_field_<?php echo esc_attr( $slug ); ?>-state">
						<?php foreach ( CCF_Constants::factory()->get_us_states() as $state ) : ?>
							<option <?php if ( ! empty( $street_post_value ) ) selected( $street_post_value, $state ); ?>><?php echo $state; ?></option>
						<?php endforeach; ?>
					</select>
					<?php if ( ! empty( $errors['state_required'] ) ) : ?>
						<div class="error"><?php echo esc_html( $errors['state_required'] ); ?></div>
					<?php endif; ?>
					<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-state" class="sub-label">State</label>

				</div>
				<div class="left">
					<input value="<?php if ( ! empty( $zipcode_post_value ) ) echo esc_attr( $zipcode_post_value ); ?>" class="<?php if ( ! empty( $errors['zipcode_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> type="text" name="ccf_field_<?php echo esc_attr( $slug ); ?>[zipcode]" id="ccf_field_<?php echo esc_attr( $slug ); ?>-zipcode">
					<?php if ( ! empty( $errors['zipcode_required'] ) ) : ?>
						<div class="error"><?php echo esc_html( $errors['zipcode_required'] ); ?></div>
					<?php endif; ?>
					<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-zipcode" class="sub-label">ZIP Code</label>

				</div>
			<?php } else if ( $address_type === 'international' ) { ?>
				<div class="right">
					<input value="<?php if ( ! empty( $state_post_value ) ) echo esc_attr( $state_post_value ); ?>" class="<?php if ( ! empty( $errors['state_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> type="text" name="ccf_field_<?php echo esc_attr( $slug ); ?>[state]" id="ccf_field_<?php echo esc_attr( $slug ); ?>-state">
					<?php if ( ! empty( $errors['state_required'] ) ) : ?>
						<div class="error"><?php echo esc_html( $errors['state_required'] ); ?></div>
					<?php endif; ?>
					<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-state" class="sub-label">State / Region / Province</label>

				</div>
				<div class="left">
					<input value="<?php if ( ! empty( $zipcode_post_value ) ) echo esc_attr( $zipcode_post_value ); ?>" class="<?php if ( ! empty( $errors['zipcode_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> type="text" name="ccf_field_<?php echo esc_attr( $slug ); ?>[zipcode]" id="ccf_field_<?php echo esc_attr( $slug ); ?>-zipcode">
					<?php if ( ! empty( $errors['zipcode_required'] ) ) : ?>
						<div class="error"><?php echo esc_html( $errors['zipcode_required'] ); ?></div>
					<?php endif; ?>
					<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-zipcode" class="sub-label">ZIP / Postal Code</label>

				</div>
				<div class="right">
					<select class="<?php if ( ! empty( $errors['country_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> name="ccf_field_<?php echo esc_attr( $slug ); ?>[country]" id="ccf_field_<?php echo esc_attr( $slug ); ?>-country">
						<?php foreach ( CCF_Constants::factory()->get_countries() as $country ) : ?>
							<option <?php if ( ! empty( $country_post_value ) ) selected( $country_post_value, $country ); ?>><?php echo $country; ?></option>
						<?php endforeach; ?>
					</select>
					<?php if ( ! empty( $errors['country_required'] ) ) : ?>
						<div class="error"><?php echo esc_html( $errors['country_required'] ); ?></div>
					<?php endif; ?>
					<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-country" class="sub-label">Country</label>

				</div>
			<?php } ?>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get phone field HTML, including any errors from the last form submission. if there is an error the
	 * field will remember it's last submitted value.
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function phone( $field_id, $form_id ) {
		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$label = get_post_meta( $field_id, 'ccf_field_label', true );
		$value = get_post_meta( $field_id, 'ccf_field_value', true );
		$placeholder = get_post_meta( $field_id, 'ccf_field_placeholder', true );
		$required = get_post_meta( $field_id, 'ccf_field_required', true );
		$class_name = get_post_meta( $field_id, 'ccf_field_className', true );

		$errors = CCF_Form_Handler::factory()->get_errors( $form_id, $slug );
		$all_errors = CCF_Form_Handler::factory()->get_errors( $form_id );

		if ( ! empty( $all_errors ) ) {
			if ( apply_filters( 'ccf_show_last_field_value', true, $field_id ) ) {
				if ( ! empty( $_POST['ccf_field_' . $slug] ) ) {
					$post_value = $_POST['ccf_field_' . $slug];
				}
			}
		}

		ob_start();
		?>

		<div data-field-type="single-line-text" class="<?php if ( ! empty( $errors ) ) : ?>field-error<?php endif; ?> field <?php echo esc_attr( $slug ); ?> single-line-text field-<?php echo (int) $field_id; ?> <?php echo esc_attr( $class_name ); ?> <?php if ( ! empty( $required ) ) : ?>field-required<?php endif; ?>">
			<label for="ccf_field_<?php echo esc_attr( $slug ); ?>">
				<?php if ( ! empty( $required ) ) : ?><span class="required">*</span><?php endif; ?>
				<?php echo esc_html( $label ); ?>
			</label>
			<input class="<?php if ( ! empty( $errors ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> type="text" name="ccf_field_<?php echo esc_attr( $slug ); ?>" id="ccf_field_<?php echo esc_attr( $slug ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="<?php if ( ! empty( $post_value ) ) { echo esc_attr( $post_value ); } else { echo esc_attr( $value ); } ?>">

			<?php if ( ! empty( $errors ) ) : ?>
				<?php foreach ( $errors as $error ) : ?>
					<div class="error"><?php echo esc_html( $error ); ?></div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get website field HTML, including any errors from the last form submission. if there is an error the
	 * field will remember it's last submitted value.
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function website( $field_id, $form_id ) {
		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$label = get_post_meta( $field_id, 'ccf_field_label', true );
		$value = get_post_meta( $field_id, 'ccf_field_value', true );
		$placeholder = get_post_meta( $field_id, 'ccf_field_placeholder', true );
		$required = get_post_meta( $field_id, 'ccf_field_required', true );
		$class_name = get_post_meta( $field_id, 'ccf_field_className', true );

		$errors = CCF_Form_Handler::factory()->get_errors( $form_id, $slug );
		$all_errors = CCF_Form_Handler::factory()->get_errors( $form_id );

		if ( ! empty( $all_errors ) ) {
			if ( apply_filters( 'ccf_show_last_field_value', true, $field_id ) ) {
				if ( ! empty( $_POST['ccf_field_' . $slug] ) ) {
					$post_value = $_POST['ccf_field_' . $slug];
				}
			}
		}

		ob_start();
		?>

		<div data-field-type="website" class="<?php if ( ! empty( $errors ) ) : ?>field-error<?php endif; ?> field <?php echo esc_attr( $slug ); ?> website field-<?php echo (int) $field_id; ?> <?php echo esc_attr( $class_name ); ?> <?php if ( ! empty( $required ) ) : ?>field-required<?php endif; ?>">
			<label for="ccf_field_<?php echo esc_attr( $slug ); ?>">
				<?php if ( ! empty( $required ) ) : ?><span class="required">*</span><?php endif; ?>
				<?php echo esc_html( $label ); ?>
			</label>
			<input class="<?php if ( ! empty( $errors ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> type="text" name="ccf_field_<?php echo esc_attr( $slug ); ?>" id="ccf_field_<?php echo esc_attr( $slug ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="<?php if ( ! empty( $post_value ) ) { echo esc_attr( $post_value ); } else { echo esc_attr( $value ); } ?>">

			<?php if ( ! empty( $errors ) ) : foreach ( $errors as $error ) : ?>
				<div class="error"><?php echo esc_html( $error ); ?></div>
			<?php endforeach; endif; ?>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get email field HTML, including any errors from the last form submission. if there is an error the
	 * field will remember it's last submitted value.
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function email( $field_id, $form_id ) {
		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$label = get_post_meta( $field_id, 'ccf_field_label', true );
		$value = get_post_meta( $field_id, 'ccf_field_value', true );
		$placeholder = get_post_meta( $field_id, 'ccf_field_placeholder', true );
		$email_confirmation = get_post_meta( $field_id, 'ccf_field_emailConfirmation', true );
		$required = get_post_meta( $field_id, 'ccf_field_required', true );
		$class_name = get_post_meta( $field_id, 'ccf_field_className', true );

		$errors = CCF_Form_Handler::factory()->get_errors( $form_id, $slug );
		$all_errors = CCF_Form_Handler::factory()->get_errors( $form_id );

		if ( ! empty( $all_errors ) ) {
			if ( apply_filters( 'ccf_show_last_field_value', true, $field_id ) ) {
				if ( ! empty( $email_confirmation ) ) {
					if ( ! empty( $_POST['ccf_field_' . $slug]['email'] ) ) {
						$email_post_value = $_POST['ccf_field_' . $slug]['email'];
					}

					if ( ! empty( $_POST['ccf_field_' . $slug]['confirm'] ) ) {
						$confirm_post_value = $_POST['ccf_field_' . $slug]['confirm'];
					}
				} else {
					$email_post_value = $_POST['ccf_field_' . $slug];
				}
			}
		}

		ob_start();
		?>

		<div data-field-type="email" class="<?php if ( ! empty( $errors ) ) : ?>field-error<?php endif; ?> field <?php echo esc_attr( $slug ); ?> email field-<?php echo (int) $field_id; ?> <?php echo esc_attr( $class_name ); ?> <?php if ( ! empty( $required ) ) : ?>field-required<?php endif; ?>">
			<label for="ccf_field_<?php echo esc_attr( $slug ); ?>">
				<?php if ( ! empty( $required ) ) : ?><span class="required">*</span><?php endif; ?>
				<?php echo esc_html( $label ); ?>
			</label>
			<?php if ( empty( $email_confirmation ) ) { ?>
				<input class="<?php if ( ! empty( $errors ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> name="ccf_field_<?php echo esc_attr( $slug ); ?>" id="ccf_field_<?php echo esc_attr( $slug ); ?>"  placeholder="<?php if ( ! empty( $placeholder ) ) { ?><?php echo esc_attr( $placeholder ) ?><?php } else { ?>email@example.com<?php } ?>" type="text" value="<?php if ( ! empty( $email_post_value ) ) { echo esc_attr( $email_post_value ); } else { echo esc_attr( $value ); } ?>">
				<?php if ( ! empty( $errors ) ) : foreach ( $errors as $error ) : ?>
					<div class="error"><?php echo esc_html( $error ); ?></div>
				<?php endforeach; endif; ?>
			<?php } else { ?>
				<div class="left">
					<input class="field-input <?php if ( ! empty( $errors['email_required'] ) || ! empty( $errors['match'] ) || ! empty( $errors['email'] ) ) : ?>field-error-input<?php endif; ?>" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> name="ccf_field_<?php echo esc_attr( $slug ); ?>[email]" id="ccf_field_<?php echo esc_attr( $slug ); ?>" value="<?php if ( ! empty( $email_post_value ) ) { echo esc_attr( $email_post_value ); }?>" type="text">
					<?php if ( ! empty( $errors['email_required'] ) ) : ?>
						<div class="error"><?php echo esc_html( $errors['email_required'] ); ?></div>
					<?php endif; ?>
					<label for="ccf_field_<?php echo esc_attr( $slug ); ?>" class="sub-label">Email</label>
				</div>
				<div class="right">
					<input class="field-input <?php if ( ! empty( $errors['confirm_required'] ) || ! empty( $errors['match'] ) || ! empty( $errors['email'] ) ) : ?>field-error-input<?php endif; ?>" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> name="ccf_field_<?php echo esc_attr( $slug ); ?>[confirm]" id="ccf_field_<?php echo esc_attr( $slug ); ?>-confirm" value="<?php if ( ! empty( $confirm_post_value ) ) { echo esc_attr( $confirm_post_value ); } ?>" type="text">
					<?php if ( ! empty( $errors['confirm_required'] ) ) : ?>
						<div class="error"><?php echo esc_html( $errors['confirm_required'] ); ?></div>
					<?php endif; ?>
					<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-confirm" class="sub-label">Confirm Email</label>
				</div>
				<?php if ( ! empty( $errors['match'] ) ) : ?>
					<div class="error"><?php echo esc_html( $errors['match'] ); ?></div>
				<?php endif; ?>
				<?php if ( ! empty( $errors['email'] ) ) : ?>
					<div class="error"><?php echo esc_html( $errors['email'] ); ?></div>
				<?php endif; ?>
			<?php } ?>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get name field HTML, including any errors from the last form submission. if there is an error the
	 * field will remember it's last submitted value.
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function name( $field_id, $form_id ) {
		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$label = get_post_meta( $field_id, 'ccf_field_label', true );
		$required = get_post_meta( $field_id, 'ccf_field_required', true );
		$class_name = get_post_meta( $field_id, 'ccf_field_className', true );

		$errors = CCF_Form_Handler::factory()->get_errors( $form_id, $slug );
		$all_errors = CCF_Form_Handler::factory()->get_errors( $form_id );

		if ( ! empty( $all_errors ) ) {
			if ( apply_filters( 'ccf_show_last_field_value', true, $field_id ) ) {
				if ( ! empty( $_POST['ccf_field_' . $slug]['first'] ) ) {
					$first_post_value = $_POST['ccf_field_' . $slug]['first'];
				}

				if ( ! empty( $_POST['ccf_field_' . $slug]['last'] ) ) {
					$last_post_value = $_POST['ccf_field_' . $slug]['last'];
				}
			}
		}

		ob_start();
		?>

		<div data-field-type="name" class="<?php if ( ! empty( $errors ) ) : ?>field-error<?php endif; ?> field <?php echo esc_attr( $slug ); ?> name field-<?php echo (int) $field_id; ?> <?php echo esc_attr( $class_name ); ?> <?php if ( ! empty( $required ) ) : ?>field-required<?php endif; ?>">
			<label>
				<?php if ( ! empty( $required ) ) : ?><span class="required">*</span><?php endif; ?>
				<?php echo esc_html( $label ); ?>
			</label>
			<div class="left">
				<input value="<?php if ( ! empty( $first_post_value ) ) echo esc_attr( $first_post_value ); ?>" class="<?php if ( ! empty( $errors['first_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> type="text" name="ccf_field_<?php echo esc_attr( $slug ); ?>[first]" id="ccf_field_<?php echo esc_attr( $slug ); ?>-first">
				<?php if ( ! empty( $errors['first_required'] ) ) : ?>
					<div class="error"><?php echo esc_html( $errors['first_required'] ); ?></div>
				<?php endif; ?>
				<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-first" class="sub-label">First</label>
			</div>
			<div class="right">
				<input value="<?php if ( ! empty( $last_post_value ) ) echo esc_attr( $last_post_value ); ?>" class="<?php if ( ! empty( $errors['last_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> type="text" name="ccf_field_<?php echo esc_attr( $slug ); ?>[last]" id="ccf_field_<?php echo esc_attr( $slug ); ?>-last">
				<?php if ( ! empty( $errors['last_required'] ) ) : ?>
					<div class="error"><?php echo esc_html( $errors['last_required'] ); ?></div>
				<?php endif; ?>
				<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-last" class="sub-label">Last</label>
			</div>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get date field HTML, including any errors from the last form submission. if there is an error the
	 * field will remember it's last submitted value.
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function date( $field_id, $form_id ) {
		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$label = get_post_meta( $field_id, 'ccf_field_label', true );
		$required = get_post_meta( $field_id, 'ccf_field_required', true );
		$class_name = get_post_meta( $field_id, 'ccf_field_className', true );
		$show_date = get_post_meta( $field_id, 'ccf_field_showDate', true );
		$show_time = get_post_meta( $field_id, 'ccf_field_showTime', true );

		$errors = CCF_Form_Handler::factory()->get_errors( $form_id, $slug );
		$all_errors = CCF_Form_Handler::factory()->get_errors( $form_id );

		$value = get_post_meta( $field_id, 'ccf_field_value', true );

		if ( ! empty( $all_errors ) ) {
			if ( apply_filters( 'ccf_show_last_field_value', true, $field_id ) ) {
				if ( ! empty( $_POST['ccf_field_' . $slug]['date'] ) ) {
					$date_post_value = $_POST['ccf_field_' . $slug]['date'];
				}

				if ( ! empty( $_POST['ccf_field_' . $slug]['hour'] ) ) {
					$hour_post_value = $_POST['ccf_field_' . $slug]['hour'];
				}

				if ( ! empty( $_POST['ccf_field_' . $slug]['minute'] ) ) {
					$minute_post_value = $_POST['ccf_field_' . $slug]['minute'];
				}

				if ( ! empty( $_POST['ccf_field_' . $slug]['am-pm'] ) ) {
					$am_pm_post_value = $_POST['ccf_field_' . $slug]['am-pm'];
				}
			}
		}

		ob_start();
		?>

		<div data-field-type="date" class="<?php if ( ! empty( $errors ) ) : ?>field-error<?php endif; ?> field <?php echo esc_attr( $slug ); ?> date field-<?php echo (int) $field_id; ?> <?php echo esc_attr( $class_name ); ?> <?php if ( ! empty( $required ) ) : ?>field-required<?php endif; ?>">
			<label for="ccf_field_<?php echo esc_attr( $slug ); ?>">
				<?php if ( ! empty( $required ) ) : ?><span class="required">*</span><?php endif; ?>
				<?php echo esc_html( $label ); ?>
			</label>
			<?php if ( ! empty( $show_date ) && empty( $show_time ) ) { ?>
				<input <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> name="ccf_field_<?php echo esc_attr( $slug ); ?>[date]" value="<?php if ( ! empty( $date_post_value ) ) { echo esc_attr( $date_post_value ); } else { echo esc_attr( $value ); } ?>" class="<?php if ( ! empty( $errors ) ) : ?>field-error-input<?php endif; ?> ccf-datepicker field-input" id="ccf_field_<?php echo esc_attr( $slug ); ?>" type="text">
			<?php } else if ( empty( $show_date ) && ! empty( $show_time ) ) { ?>
				<div class="hour">
					<input maxlength="2" class="<?php if ( ! empty( $errors['hour_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> name="ccf_field_<?php echo esc_attr( $slug ); ?>[hour]" value="<?php if ( ! empty( $hour_post_value ) ) { echo esc_attr( $hour_post_value ); } ?>" id="ccf_field_<?php echo esc_attr( $slug ); ?>-hour" type="text">
					<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-hour" class="sub-label">HH</label>
				</div>
				<div class="minute">
					<input maxlength="2" class="<?php if ( ! empty( $errors['minutes_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> name="ccf_field_<?php echo esc_attr( $slug ); ?>[minute]" value="<?php if ( ! empty( $minute_post_value ) ) { echo esc_attr( $minute_post_value ); } ?>" id="ccf_field_<?php echo esc_attr( $slug ); ?>-minute" type="text">
					<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-minute" class="sub-label">MM</label>
				</div>
				<div class="am-pm">
					<select class="<?php if ( ! empty( $errors['am-pm_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> name="ccf_field_<?php echo esc_attr( $slug ); ?>[am-pm]" id="ccf_field_<?php echo esc_attr( $slug ); ?>-am-pm">
						<option <?php if ( ! empty( $am_pm_post_value ) ) { selected( 'am', $am_pm_post_value ); } ?> value="am">AM</option>
						<option <?php if ( ! empty( $am_pm_post_value ) ) { selected( 'pm', $am_pm_post_value ); } ?> value="pm">PM</option>
					</select>
				</div>
			<?php } else { ?>
				<div class="left">
					<input value="<?php if ( ! empty( $date_post_value ) ) { echo esc_attr( $date_post_value ); } ?>" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> name="ccf_field_<?php echo esc_attr( $slug ); ?>[date]" class="<?php if ( ! empty( $errors['date_required'] ) ) : ?>field-error-input<?php endif; ?> ccf-datepicker field-input" id="ccf_field_<?php echo esc_attr( $slug ); ?>-date" type="text">
					<label for="ccf_field_<?php echo esc_attr( $slug ); ?>-date" class="sub-label">Date</label>
				</div>
				<div class="right">
					<div class="hour">
						<input class="<?php if ( ! empty( $errors['hour_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> maxlength="2" name="ccf_field_<?php echo esc_attr( $slug ); ?>[hour]" value="<?php if ( ! empty( $hour_post_value ) ) { echo esc_attr( $hour_post_value ); } ?>" id="ccf_field_<?php echo esc_attr( $slug ); ?>-hour" type="text">
						<label class="sub-label" for="ccf_field_<?php echo esc_attr( $slug ); ?>-hour">HH</label>
					</div>
					<div class="minute">
						<input class="<?php if ( ! empty( $errors['minutes_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> maxlength="2" name="ccf_field_<?php echo esc_attr( $slug ); ?>[minute]" value="<?php if ( ! empty( $minute_post_value ) ) { echo esc_attr( $minute_post_value ); } ?>" id="ccf_field_<?php echo esc_attr( $slug ); ?>-minute" type="text">
						<label class="sub-label" for="ccf_field_<?php echo esc_attr( $slug ); ?>-minute">MM</label>
					</div>
					<div class="am-pm">
						<select class="<?php if ( ! empty( $errors['am-pm_required'] ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> name="ccf_field_<?php echo esc_attr( $slug ); ?>[am-pm]" id="ccf_field_<?php echo esc_attr( $slug ); ?>-am-pm">
							<option <?php if ( ! empty( $am_pm_post_value ) ) { selected( 'am', $am_pm_post_value ); } ?> value="am">AM</option>
							<option <?php if ( ! empty( $am_pm_post_value ) ) { selected( 'pm', $am_pm_post_value ); } ?> value="pm">PM</option>
						</select>
					</div>
				</div>
			<?php } ?>
			<?php if ( ! empty( $errors ) ) : foreach ( $errors as $error ) : ?>
				<div class="error"><?php echo esc_html( $error ); ?></div>
			<?php endforeach; endif; ?>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get paragraph-text field HTML, including any errors from the last form submission. if there is an
	 * error the field will remember it's last submitted value.
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function paragraph_text( $field_id, $form_id ) {
		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$label = get_post_meta( $field_id, 'ccf_field_label', true );
		$value = get_post_meta( $field_id, 'ccf_field_value', true );
		$placeholder = get_post_meta( $field_id, 'ccf_field_placeholder', true );
		$required = get_post_meta( $field_id, 'ccf_field_required', true );
		$class_name = get_post_meta( $field_id, 'ccf_field_className', true );

		$errors = CCF_Form_Handler::factory()->get_errors( $form_id, $slug );
		$all_errors = CCF_Form_Handler::factory()->get_errors( $form_id );

		if ( ! empty( $all_errors ) ) {
			if ( apply_filters( 'ccf_show_last_field_value', true, $field_id ) ) {
				if ( ! empty( $_POST['ccf_field_' . $slug] ) ) {
					$post_value = $_POST['ccf_field_' . $slug];
				}
			}
		}

		ob_start();
		?>

		<div data-field-type="paragraph-text" class="<?php if ( ! empty( $errors ) ) : ?>field-error<?php endif; ?> field <?php echo esc_attr( $slug ); ?> paragraph-text field-<?php echo (int) $field_id; ?> <?php echo esc_attr( $class_name ); ?> <?php if ( ! empty( $required ) ) : ?>field-required<?php endif; ?>">
			<label for="ccf_field_<?php echo esc_attr( $slug ); ?>">
				<?php if ( ! empty( $required ) ) : ?><span class="required">*</span><?php endif; ?>
				<?php echo esc_html( $label ); ?>
			</label>
			<textarea class="<?php if ( ! empty( $errors ) ) : ?>field-error-input<?php endif; ?> field-input" <?php if ( ! empty( $required ) ) : ?>required aria-required="true"<?php endif; ?> name="ccf_field_<?php echo esc_attr( $slug ); ?>" id="ccf_field_<?php echo esc_attr( $slug ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"><?php if ( ! empty( $post_value ) ) { echo esc_attr( $post_value ); } else { echo esc_attr( $value ); } ?></textarea>

			<?php if ( ! empty( $errors ) ) : ?>
				<div class="error"><?php echo esc_html( $errors['required'] ); ?></div>
			<?php endif; ?>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get hidden field HTML
	 *
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function hidden( $field_id, $form_id ) {
		$slug = get_post_meta( $field_id, 'ccf_field_slug', true );
		$value = get_post_meta( $field_id, 'ccf_field_value', true );

		ob_start();
		?>

		<input type="hidden" name="ccf_field_<?php echo esc_attr( $slug ); ?>" id="ccf_field_<?php echo esc_attr( $slug ); ?>" value="<?php echo esc_attr( $value ); ?>">

		<?php
		return ob_get_clean();
	}

	/**
	 * Route field rendering requests to field specific method and return html for given field
	 *
	 * @param string $type
	 * @param int $field_id
	 * @param int $form_id
	 * @since 6.0
	 * @return string
	 */
	public function render_router( $type, $field_id, $form_id ) {
		$field_html = '';

		switch ( $type ) {
			case 'single-line-text':
				$field_html = $this->single_line_text( $field_id, $form_id );
				break;
			case 'hidden':
				$field_html = $this->hidden( $field_id, $form_id );
				break;
			case 'paragraph-text':
				$field_html = $this->paragraph_text( $field_id, $form_id );
				break;
			case 'dropdown':
				$field_html = $this->dropdown( $field_id, $form_id );
				break;
			case 'checkboxes':
				$field_html = $this->checkboxes( $field_id, $form_id );
				break;
			case 'radio':
				$field_html = $this->radio( $field_id, $form_id );
				break;
			case 'html':
				$field_html = $this->html( $field_id, $form_id );
				break;
			case 'section-header':
				$field_html = $this->section_header( $field_id, $form_id );
				break;
			case 'name':
				$field_html = $this->name( $field_id, $form_id );
				break;
			case 'date':
				$field_html = $this->date( $field_id, $form_id );
				break;
			case 'address':
				$field_html = $this->address( $field_id, $form_id );
				break;
			case 'website':
				$field_html = $this->website( $field_id, $form_id );
				break;
			case 'phone':
				$field_html = $this->phone( $field_id, $form_id );
				break;
			case 'email':
				$field_html = $this->email( $field_id, $form_id );
				break;
		}

		return $field_html;
	}
	/**
	 * Return singleton instance of class
	 *
	 * @since 6.0
	 * @return object
	 */
	public static function factory() {
		static $instance;

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}
}