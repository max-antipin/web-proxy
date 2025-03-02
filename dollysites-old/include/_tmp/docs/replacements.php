<?php
namespace DollySites\Docs;
use \DollySites as DS;
use \MaxieSystems as MS;
use \MaxieSystems\DB;
use \MaxieSystems\Config;
use \MaxieSystems\Document\UI;
use \MaxieSystems\HTML;
use \MaxieSystems\HTTP;
use \MaxieSystems\L10N;
use Select;
use Filter;

class Replacements extends MS\Document
{
	use DS\TReplacements;

	final public function Show()
	 {
		$this->AddJS('replacements')->AddCSS('replacements');
        $new = $this->Load();
?><script type="text/javascript">
    var FIND_WORD = '<?=l10n()->find_what?>';
    var REPLACE_WORD = '<?=l10n()->replace_with?>';
    var SET_TEXT = '<?=l10n()->enter_text?>';
    var PREG = '<?=l10n()->regex?>';
</script>
<form action="core.php?action=add_replacement&sub=edit" method="post" id="form">
    <div class="text_changer">
        <div id="replaces">
            <div class="fieldtitle">
                <div class="left">
                    <label class="label"><?=l10n()->find_what?></label>
                </div>
                <div class="right">
                    <label class="label"><?=l10n()->replace_with?></label>
                </div>
            </div>
            <?php if (!$new || !sizeof($new)) {
                $this->printReplace(1, null, $this);
            } else {
                if (isset($new) AND is_array($new)) {
                    $i = 0;
                    foreach ($new as $key => $value) {
                        if (@$value['change_type'] == 'script') {
                            continue;
                        }
                        ++$i;

                        $this->printReplace($key, $value, $this);
                    }
                    if (!$i) {
                        $this->printReplace(1, null, $this);
                    }
                }
            } ?>
        </div>
        <div class="buttons"><?=new HTML\Input\Button(['value' => l10n()->add_replacement, 'class' => 'msui_small_button _icon _add add_column'])?></div>
    </div>
	<?=new HTML\Input\Submit(['value' => l10n()->save, 'class' => 'msui_button'])?>
</form><?php
	 }

	final public function Handle()
	 {
		$out = empty($_POST['out']) ? [] : array_merge($_POST['out'], array());
		if ($_GET['sub'] === 'editor') {
			throw new \Exception('not implemented yet...');
			// $replaces = self::dolly_unserialize(file_get_contents($this->fname));
			// $replaces[] = $out[0];

			// file_put_contents($this->fname, self::dolly_serialize($replaces));
		} else {
			if (!isset($out)) {
				$out = array();
			}

			foreach ($out as $key => &$value) {
				if (!$value['l_input'] and $value['l_textarea']) {
					$value['l_input'] = $value['l_textarea'];
				}
				if (!$value['r_input'] and $value['r_textarea']) {
					$value['r_input'] = $value['r_textarea'];
				}

				if ($value['l_textarea']) {
					$value['change_type'] = !empty($value['change_type']) ? 'preg' : 'string';

					$value['l_input'] = htmlspecialchars($value['l_textarea']);
					$value['r_input'] = htmlspecialchars($value['r_textarea']);
				} else {
					unset($out[$key]);
				}

			}

			if($this->Save($out)) $this->AddSuccessMsg($this->L10N()->changes_saved_successfully);
		}
		//CacheBackend::clearCache(null, 'pages');
	 }

	protected function printReplace($key=1, $value=array(), $self = null, $first = false)
	 {
?><div class="fields">
	<div class="column" id="column_<?php echo $key;?>">
		<div class="input_wrap">
			<div class="remove"></div>
			<div class="left pd0">
				<input type="text" name="out[<?php echo $key;?>][l_input]" class="textbox" placeholder="<?=l10n()->enter_text?>" value="<?php echo @strip_tags($value['l_input']);?>">
			</div>
			<div class="right pd0">
				<input type="text" name="out[<?php echo $key;?>][r_input]" class="textbox" placeholder="<?=l10n()->enter_text?>" value="<?php echo @strip_tags($value['r_input']);?>">
			</div>
		</div>
		<div class="textarea_wrap hidden">
			<div class="remove"></div>
			<div class="left pd0">
				<textarea name="out[<?php echo $key;?>][l_textarea]" class="magic_textarea" placeholder="<?=l10n()->enter_text?>"><?php echo htmlspecialchars_decode($value['l_textarea']);?></textarea>
			</div>
			<div class="right pd0">
				<textarea name="out[<?=$key?>][r_textarea]" placeholder="<?=l10n()->enter_text?>" class="magic_textarea"><?=isset($value['r_textarea']) ? htmlspecialchars_decode($value['r_textarea']) : ''?></textarea>
			</div>
			<div class="change_wrap">
				<div class="change_type">
					<input type="checkbox" class="super_checkbox" name="out[<?=$key?>][change_type]" id="regular[<?=$key?>]" <?php echo ($value['change_type'] == 'preg') ? 'checked' : null;?>>
					<label for="regular[<?php echo $key;?>]" class="label"><?=l10n()->regex?></label>
				</div>
			</div>
		</div>
	</div>
</div><?php
	 }
}
?>