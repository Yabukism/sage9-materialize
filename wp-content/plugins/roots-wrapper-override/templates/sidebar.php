<p><b><?php echo $description; ?></b><sup>*</sup></p>
<p>
  <input type="radio" name="_roots_rwo_override_sidebar" value="show"  <?php checked($sidebar_override, 'show'); ?>/> <?php echo $show; ?><br />
  <input type="radio" name="_roots_rwo_override_sidebar" value="hide" <?php checked($sidebar_override, 'hide'); ?>/> <?php echo $hide; ?><br />
  <input type="radio" name="_roots_rwo_override_sidebar" value="none"  <?php checked($sidebar_override, ''); ?>/> <?php echo $default; ?><br />
</p>
<p>
  <small><sup>*</sup> <?php echo $help; ?></small>
</p>