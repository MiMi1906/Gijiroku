<div class="block post <?php if (!empty($v->reply_post_id && $v->reply_post_id > 0)) : ?>reply<?php endif; ?>">
  <div class="image">
    <a href="/profile/?id=<?php echo h($v->member_id); ?>">
      <img src="/resource/image/icon/<?php echo h($v->image); ?>" alt="<?php echo h($v->name) ?>">
    </a>
  </div>

  <div class="text">
    <div class="link" id="p<?php echo $v->thread_id; ?>">
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
          <a href="/post/?res=<?php echo h($v->id) ?>">
            <i class="fas fa-reply"></i>
          </a>
        </object>
      </div>
      <div class="menu_list quote">
        <object data="" type="">
          <a href="/post/?quote=<?php echo h($v->id); ?>"><i class="fas fa-quote-right"></i></a>
        </object>
      </div>
      <div class="menu_list like" id="<?php echo $v->id; ?>">
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
            <a href="/thread/?id=<?php echo h($v->thread_id); ?>">スレッドを表示</a>
          </object>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>