<div class="block post <?php if (!empty($v->reply_post_id && $v->reply_post_id > 0)) : ?>reply <?php endif; ?><?php if ($v->member_id == $_SESSION['id']) : ?>my_post<?php endif; ?> <?php if (!empty($_REQUEST['emph_id']) && $_REQUEST['emph_id'] == $v->id) {
                                                                                                                                                                                        echo 'emph';
                                                                                                                                                                                      } ?>" id="<?php echo $v->id; ?>">
  <div class="image">
    <a href="/profile/?id=<?php echo h($v->member_id); ?>">
      <img src="<?php echo h($v->image); ?>" alt="<?php echo h($v->name) ?>">
    </a>
  </div>

  <div class="text">
    <div class="link" id="p<?php echo $v->thread_id; ?> e<?php echo $v->id; ?>">
      <div class="heading">
        <div class="name">
          <?php echo h($v->name); ?>
        </div>
        <div class="created">
          <?php echo h(fuzzyTime($v->created)); ?>
        </div>
      </div>
      <div class="message">
        <object data="" type=""><?php echo coloring($v->message); ?></object>
      </div>
    </div>
    <div class="menu_group">
      <div class="menu_list reply">
        <object data="" type="">
          <a href="/?res=<?php echo h($v->id) ?>#send_window">
            <i class="fas fa-reply"></i>
          </a>
        </object>
      </div>
      <div class="menu_list quote">
        <object data="" type="">
          <a href="/?quote=<?php echo h($v->id); ?>#send_window"><i class="fas fa-quote-right"></i></a>
        </object>
      </div>
      <div class="menu_list like" id="n<?php echo $v->id; ?>">
        <object>
          <?php if ($v->like_str != 0) echo $v->like_str; ?>
        </object>
      </div>
      <?php if ($_SESSION['id'] === $v->member_id) : ?>
        <div class="menu_list delete">
          <object data="" type="">
            <a href="/delete/?id=<?php echo h($v->id); ?>"><i class="far fa-trash-alt"></i></a>
          </object>
        </div>
      <?php endif; ?>
      <?php if (!empty($v->reply_post_id && $v->reply_post_id > 0)) : ?>
        <div class="menu_list show_relpy">
          <object data="" type="">
            <?php if ($_SERVER['REQUEST_URI'] == "/") : ?>
              <a href="/thread/?thread_id=<?php echo h($v->thread_id); ?>">スレッドを表示</a>
            <?php endif; ?>
          </object>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>