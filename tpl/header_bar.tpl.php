<header class="header">
  <div class="header_group">
    <?php if (!empty($_SESSION['id'])) : ?>
      <div class="header_list header_menu_left menu_icon">
        <?php if ($_SERVER['REQUEST_URI'] != '/') : ?>
          <a onclick="history.back();"><i class="fas fa-chevron-left"></i></a>
        <?php else : ?>
          <a href="/"><i class="fas fa-pen-nib"></i></a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <div class="header_list header_logo">
      <a href="/">
        <?php echo $v->heading; ?>
      </a>
    </div>
    <?php if (!empty($_SESSION['id'])) : ?>
      <div class="header_list header_menu_right menu_icon">
        <i class="fas fa-search"></i>
      </div>
    <?php endif; ?>
  </div>
</header>