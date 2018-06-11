<?php

/**
 * Class for SFInputImagesList extension
 *
 * @file
 * @ingroup Extensions
 */
class SFInputImagesList extends SFCheckboxesInput {

	public static function getName() {
		return 'imagelist';
	}

	public function getResourceModuleNames() {
		return array( 'ext.sfinputimageslist.foo' );
	}

	public static function getParameters() {

		$params = parent::getParameters();
		return $params;
	}

	public static function getHTML( $cur_value, $input_name, $is_mandatory, $is_disabled, $other_args ) {
		global $sfgTabIndex, $sfgFieldNum, $sfgShowOnSelect, $wgOut;

		$wgOut->addModuleStyles('ext.sfinputimageslist.foo');
		$wgOut->addModules('ext.sfinputimageslist.foo');

		$checkboxClass = ( $is_mandatory ) ? 'mandatoryField' : 'createboxInput';
		$labelClass = 'checkboxLabel';
		if ( array_key_exists( 'class', $other_args ) ) {
			$labelClass .= ' ' . $other_args['class'];
		}
		$input_id = "input_$sfgFieldNum";
		// get list delimiter - default is comma
		if ( array_key_exists( 'delimiter', $other_args ) ) {
			$delimiter = $other_args['delimiter'];
		} else {
			$delimiter = ',';
		}
		$cur_values = SFUtils::getValuesArray( $cur_value, $delimiter );

		if ( ( $possible_values = $other_args['possible_values'] ) == null ) {
			$possible_values = array();
		}
		$text = '';
		foreach ( $possible_values as $key => $possible_value ) {
			$cur_input_name = $input_name . '[' . $key . ']';

			if (
				array_key_exists( 'value_labels', $other_args ) &&
				is_array( $other_args['value_labels'] ) &&
				array_key_exists( $possible_value, $other_args['value_labels'] )
			) {
				$label = $other_args['value_labels'][$possible_value];
			} else {
				$label = $possible_value;
				$file = wfLocalFile($possible_value);
				$imgPath = $file->createThumb(100);
				$img2Path = $file->createThumb(200);
				$label = '<img srcset="'.$img2Path.' 2x" src="'.$imgPath.'" />';
			}

			$checkbox_attrs = array(
				'id' => $input_id,
				'tabindex' => $sfgTabIndex,
				'class' => $checkboxClass,
			);
			if ( in_array( $possible_value, $cur_values ) ) {
				$checkbox_attrs['checked'] = 'checked';
			}
			if ( $is_disabled ) {
				$checkbox_attrs['disabled'] = 'disabled';
			}
			$checkbox_input = Html::input( $cur_input_name, $possible_value, 'checkbox', $checkbox_attrs );

			// Put a <label> tag around each checkbox, for CSS
			// purposes as well as to clarify this element.
			$text .= "\t" . Html::rawElement( 'label',
					array( 'class' => $labelClass ),
					$checkbox_input . ' ' . $label
				) . "\n";
			$sfgTabIndex++;
			$sfgFieldNum++;
		}

		$outerSpanID = "span_$sfgFieldNum";
		$outerSpanClass = 'checkboxesSpan SFInputImagesList';
		if ( $is_mandatory ) {
			$outerSpanClass .= ' mandatoryFieldSpan';
		}

		if ( array_key_exists( 'show on select', $other_args ) ) {
			$outerSpanClass .= ' sfShowIfChecked';
			foreach ( $other_args['show on select'] as $div_id => $options ) {
				if ( array_key_exists( $outerSpanID, $sfgShowOnSelect ) ) {
					$sfgShowOnSelect[$outerSpanID][] = array( $options, $div_id );
				} else {
					$sfgShowOnSelect[$outerSpanID] = array( array( $options, $div_id ) );
				}
			}
		}

		$text .= Html::hidden( $input_name . '[is_list]', 1 );
		$outerSpanAttrs = array( 'id' => $outerSpanID, 'class' => $outerSpanClass );

		//Values from query
		if ( array_key_exists( 'values from query', $other_args ) ) {
			$outerSpanAttrs['vfq'] = $other_args['values from query'];
			$outerSpanAttrs['class'] .= ' ValuesFromQueryTarget';
			$outerSpanAttrs['inputname'] = $input_name;
			//If there some substitution, we pass it to tag parameters
			if ( array_key_exists( 'substitution', $other_args ) ) {
				$outerSpanAttrs['vfqs'] = $other_args['substitution'];
			}
		}

		$text = "\t" . Html::rawElement( 'span', $outerSpanAttrs, $text ) . "\n";

		return $text;
	}

}
