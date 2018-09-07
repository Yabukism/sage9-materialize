<p><b><?php echo ucfirst($prefix); ?> Template</b></p>
<label class="screen-reader-text" for="roots-rwo-<?php echo $prefix; ?>"><?php echo ucfirst($prefix); ?> Template</label>
<select class="widefat" id="roots-rwo-<?php echo $prefix; ?>" name="_roots_rwo_<?php echo $prefix; ?>">
  <?php if ($file != 'none') : ?><option value="none"><?php echo $this->name('none', $prefix); ?></option><?php endif; ?>
  <option value="<?php echo $file ?>" selected="selected"><?php echo $this->name($file, $prefix); ?></option>
  <?php $this->values($file, $prefix, $subdir); ?>
</select>
<input type="hidden" name="_roots_rwo_folder_<?php echo $prefix; ?>" value="<?php echo $subdir; ?>">