<?php
namespace common\customassets\wizardwidget;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Description of CustomWizardWidget
 *
 * @author AVELARE
 */
class CustomWizardWidget extends Widget {
    	/**
	 * @var string widget html id
	 */
	public $id = 'wizard';

	/**
	 * @var array default button configuration
	 */
	public $default_buttons = [
		'prev' => ['title' => 'Previous', 'options' => [ 'class' => 'btn btn-default', 'type' => 'button']],
		'next' => ['title' => 'Next', 'options' => [ 'class' => 'btn btn-default', 'type' => 'button']],
		'save' => ['title' => 'Save', 'options' => [ 'class' => 'btn btn-default', 'type' => 'button']],
		'skip' => ['title' => 'Skip', 'options' => [ 'class' => 'btn btn-default', 'type' => 'button']],
	];

	/**
	 * @var array the wizard step definition
	 */
	public $steps = [];

	/**
	 * @var integer step id to start with
	 */
	public $start_step = null;
        
        public $include_top_navigation_buttons = false;

	/**
	 * @var string optional final complete step content
	 */
	public $complete_content = '';

	/**
	 * Main entry to execute the widget
	 */
	public function run() {
		parent::run();
		CustomWizardWidgetAsset::register($this->getView());


		// Wizard line calculation
                $space = 100;
		$step_count = count($this->steps)+($this->complete_content?1:0);
		$wizard_line_distribution = floor(100 / $step_count); // Percentage
		$wizard_line_width = round(100 - $wizard_line_distribution);//($wizard_line_distribution * $step_count);//round(100 - $wizard_line_distribution); // Percentage
		$wizard_line = '';

		$tab_content = '';

		// Navigation tracker
		end($this->steps);
		$last_id = key($this->steps);

		$first = true;
		$class = '';
		foreach ($this->steps as $id => $step) {

			// Current or fist step is active, next steps are inactive (previous steps are available)
			if ($id == $this->start_step or (is_null($this->start_step) && $class == '')) {
				$class = 'active';
			} elseif ($class == 'active') {
				$class = 'disabled';
                        } else {
                            $class = 'disabled';
                        }
                        $space -= $wizard_line_distribution;
                        
			// Add icons to the wizard line
			$wizard_line .= Html::tag(
				'li',
				Html::a('<span class="round-tab"><i class="'.$step['icon'].'"></i></span>', '#step'.$id, [
					'data-toggle' => 'tab',
					'aria-controls' => 'step'.$id,
					'role' => 'tab',
					'title' => $step['title'],
				]),
				array_merge(
					[
						'role' => 'presentation',
						'class' => $class,
						'style' => [
                                                    'width' => $wizard_line_distribution.'%'
                                                ]
					],
					isset($step['options']) ? $step['options'] : []
				)
			);

			// Setup navigation buttons
			$buttons = [];
			$button_id = "{$this->id}_step{$id}_";
			if (!$first) {
				// Show previous button except on first step
				$buttons[] = $this->navButton('prev', $step, $button_id);
			}
			if (array_key_exists('skippable', $step) and $step['skippable'] === true) {
				// Show skip button if specified
				$buttons[] = $this->navButton('skip', $step, $button_id);
			}
			if ($id == $last_id) {
				// Show save button on last step
				$buttons[] = $this->navButton('save', $step, $button_id);
			} else {
				// On all previous steps show next button
				$buttons[] = $this->navButton('next', $step, $button_id);
			}
                        
			// Setup tab content
			$tab_content .= '<div class="tab-pane '.$class.'" role="tabpanel" id="step'.$id.'">';
                        if($this->include_top_navigation_buttons){
                            $buttonRow = Html::tag('span', Html::ul($buttons, ['class' => 'list-inline', 'encode' => false]), ['class' => 'text-right'] );
                            $step['content'] = $buttonRow.$step['content'];
                        }
			$tab_content .= $step['content'];

			// Add buttons to tab content
			$tab_content .= Html::ul($buttons, ['class' => 'list-inline pull-right', 'encode' => false]);

			// Finish tab
			$tab_content .= '</div>';

			$first = false;
		}

		// Add a completed step if specified
		if ($this->complete_content) {
			$class = 'disabled';

			// Check if completed tab is set as start_step
			if ($this->start_step == 'completed') {
				$class = 'active';
			}

			// Add completed icon to wizard line
			$wizard_line .= Html::tag(
				'li',
				Html::a('<span class="round-tab"><i class="fas fa-check-circle"></i></span>', '#complete', [
					'data-toggle' => 'tab',
					'aria-controls' => 'complete',
					'role' => 'tab',
					'title' => 'Complete',
				]),
				[
					'role' => 'presentation',
					'class' => $class,
					'style' => ['width' => $wizard_line_distribution.'%']
				]
			);

			$tab_content .= '<div class="tab-pane '.$class.'" role="tabpanel" id="complete">'.$this->complete_content.'</div>';
		}

		// Start widget
		echo '<div class="wizard" id="'.$this->id.'">';

		// Render the steps line
		echo '<div class="wizard-inner"><div class="connecting-line" ></div>';
        //style="width:'.$wizard_line_width.'%"
		echo '<ul class="nav nav-tabs" role="tablist">'.$wizard_line.'</ul>';
		echo '</div>';

		// Render the content tabs
		echo '<div class="tab-content">'.$tab_content.'</div>';

		// Finalize the content tabs
		echo '<div class="clearfix"></div>';

		// Finish widget
		echo '</div>';
	}

	/**
	 * Generate navigation button
	 *
	 * @param string $button_type prev|skip|next\save
	 * @param array $step step configuration
	 * @param string $button_id
	 *
	 * @return string
	 */
	protected function navButton($button_type, $step, $button_id) {
		// Setup a unique button id
		$options = ['id' => $button_id.$button_type];

		// Apply default button configuration if defined
		if (isset($this->default_buttons[$button_type]['options'])) {
			$options = array_merge($options, $this->default_buttons[$button_type]['options']);
		}

		// Apply step specific button configuration if defined
		if (isset($step['buttons'][$button_type]['options'])) {
			$options = array_merge($options, $step['buttons'][$button_type]['options']);
		}

		// Add navigation class
		if ($button_type == 'prev') {
			$options['class'] = $options['class'].' prev-step';
		} else {
			$options['class'] = $options['class'].' next-step';
		}

		// Display button
		if (isset($step['buttons'][$button_type]['html'])) {
			return $step['buttons'][$button_type]['html'];
		} elseif (isset($step['buttons'][$button_type]['title'])) {
			return Html::button($step['buttons'][ $button_type ]['title'], $options);
		} else {
			return Html::button($this->default_buttons[ $button_type ]['title'], $options);
		}
	}
}
