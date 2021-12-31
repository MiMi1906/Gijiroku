<header class="header">
  <div class="header_group">
    <div class="header_list header_menu_left menu_icon">
      <?php if ($_SERVER['REQUEST_URI'] != '/') : ?>
        <a href="/"><i class="fas fa-chevron-left "></i></a>
      <?php else : ?>
        <i class="fas fa-user-circle"></i>
      <?php endif; ?>
    </div>
    <div class="header_list header_logo">
      <a href="/">
        Gijiroku
      </a>
    </div>
    <div class="header_list header_menu_right menu_icon">
      <i class="fas fa-search"></i>
    </div>
  </div>
</header>