<?php
$data = array (
  'exp' => 0,
  'data' => 
  array (
    'forum' => 
    array (
      'forum_trade' => 
      array (
        'group_trade' => 
        array (
          'name' => '群组商品',
          'script' => 'grouptrade',
          'searchkeys' => 
          array (
          ),
          'replacekeys' => 
          array (
          ),
        ),
      ),
      'forum_forum' => 
      array (
        'group_group' => 
        array (
          'name' => '群组模块',
          'script' => 'group',
          'searchkeys' => 
          array (
          ),
          'replacekeys' => 
          array (
          ),
        ),
        'portal_category' => 
        array (
          'name' => '文章栏目',
          'script' => 'portalcategory',
          'searchkeys' => 
          array (
            0 => 'threads',
          ),
          'replacekeys' => 
          array (
            0 => 'articles',
          ),
        ),
      ),
      'forum_activity' => 
      array (
        'group_activity' => 
        array (
          'name' => '群组活动',
          'script' => 'groupactivity',
          'searchkeys' => 
          array (
          ),
          'replacekeys' => 
          array (
          ),
        ),
      ),
      'forum_thread' => 
      array (
        'portal_article' => 
        array (
          'name' => '文章模块',
          'script' => 'article',
          'searchkeys' => 
          array (
            0 => 'author',
            1 => 'authorid',
            2 => 'forumurl',
            3 => 'forumname',
            4 => 'posts',
            5 => 'views',
            6 => 'replies',
          ),
          'replacekeys' => 
          array (
            0 => 'username',
            1 => 'uid',
            2 => 'caturl',
            3 => 'catname',
            4 => 'articles',
            5 => 'viewnum',
            6 => 'commentnum',
          ),
        ),
        'space_blog' => 
        array (
          'name' => '日志模块',
          'script' => 'blog',
          'searchkeys' => 
          array (
            0 => 'author',
            1 => 'authorid',
            2 => 'views',
            3 => 'replies',
          ),
          'replacekeys' => 
          array (
            0 => 'username',
            1 => 'uid',
            2 => 'viewnum',
            3 => 'replynum',
          ),
        ),
        'group_thread' => 
        array (
          'name' => '群组帖子',
          'script' => 'groupthread',
          'searchkeys' => 
          array (
            0 => 'forumname',
            1 => 'forumurl',
          ),
          'replacekeys' => 
          array (
            0 => 'groupname',
            1 => 'groupurl',
          ),
        ),
      ),
    ),
    'group' => 
    array (
      'group_trade' => 
      array (
        'forum_trade' => 
        array (
          'name' => '商品模块',
          'script' => 'trade',
          'searchkeys' => 
          array (
          ),
          'replacekeys' => 
          array (
          ),
        ),
      ),
      'group_thread' => 
      array (
        'portal_article' => 
        array (
          'name' => '文章模块',
          'script' => 'article',
          'searchkeys' => 
          array (
            0 => 'author',
            1 => 'authorid',
            2 => 'groupurl',
            3 => 'groupname',
            4 => 'posts',
            5 => 'views',
            6 => 'replies',
          ),
          'replacekeys' => 
          array (
            0 => 'username',
            1 => 'uid',
            2 => 'caturl',
            3 => 'catname',
            4 => 'articles',
            5 => 'viewnum',
            6 => 'commentnum',
          ),
        ),
        'space_blog' => 
        array (
          'name' => '日志模块',
          'script' => 'blog',
          'searchkeys' => 
          array (
            0 => 'author',
            1 => 'authorid',
            2 => 'views',
            3 => 'replies',
          ),
          'replacekeys' => 
          array (
            0 => 'username',
            1 => 'uid',
            2 => 'viewnum',
            3 => 'replynum',
          ),
        ),
        'forum_thread' => 
        array (
          'name' => '帖子模块',
          'script' => 'thread',
          'replacekeys' => 
          array (
            0 => 'forumname',
            1 => 'forumurl',
          ),
          'searchkeys' => 
          array (
            0 => 'groupname',
            1 => 'groupurl',
          ),
        ),
      ),
      'group_activity' => 
      array (
        'forum_activity' => 
        array (
          'name' => '活动模块',
          'script' => 'activity',
          'searchkeys' => 
          array (
          ),
          'replacekeys' => 
          array (
          ),
        ),
      ),
      'group_group' => 
      array (
        'forum_forum' => 
        array (
          'name' => '版块模块',
          'script' => 'forum',
          'searchkeys' => 
          array (
          ),
          'replacekeys' => 
          array (
          ),
        ),
        'portal_category' => 
        array (
          'name' => '文章栏目',
          'script' => 'portalcategory',
          'searchkeys' => 
          array (
            0 => 'threads',
          ),
          'replacekeys' => 
          array (
            0 => 'articles',
          ),
        ),
      ),
    ),
    'portal' => 
    array (
      'portal_article' => 
      array (
        'forum_thread' => 
        array (
          'name' => '帖子模块',
          'script' => 'thread',
          'searchkeys' => 
          array (
            0 => 'username',
            1 => 'uid',
            2 => 'caturl',
            3 => 'catname',
            4 => 'articles',
            5 => 'viewnum',
            6 => 'commentnum',
          ),
          'replacekeys' => 
          array (
            0 => 'author',
            1 => 'authorid',
            2 => 'forumurl',
            3 => 'forumname',
            4 => 'posts',
            5 => 'views',
            6 => 'replies',
          ),
        ),
        'group_thread' => 
        array (
          'name' => '群组帖子',
          'script' => 'groupthread',
          'searchkeys' => 
          array (
            0 => 'username',
            1 => 'uid',
            2 => 'caturl',
            3 => 'catname',
            4 => 'articles',
            5 => 'viewnum',
            6 => 'commentnum',
          ),
          'replacekeys' => 
          array (
            0 => 'author',
            1 => 'authorid',
            2 => 'groupurl',
            3 => 'groupname',
            4 => 'posts',
            5 => 'views',
            6 => 'replies',
          ),
        ),
        'space_blog' => 
        array (
          'name' => '日志模块',
          'script' => 'blog',
          'searchkeys' => 
          array (
            0 => 'commentnum',
          ),
          'replacekeys' => 
          array (
            0 => 'replynum',
          ),
        ),
      ),
      'portal_category' => 
      array (
        'forum_forum' => 
        array (
          'name' => '版块模块',
          'script' => 'forum',
          'searchkeys' => 
          array (
            0 => 'articles',
          ),
          'replacekeys' => 
          array (
            0 => 'threads',
          ),
        ),
        'group_group' => 
        array (
          'name' => '群组模块',
          'script' => 'group',
          'searchkeys' => 
          array (
            0 => 'articles',
          ),
          'replacekeys' => 
          array (
            0 => 'threads',
          ),
        ),
      ),
    ),
    'space' => 
    array (
      'space_blog' => 
      array (
        'forum_thread' => 
        array (
          'name' => '帖子模块',
          'script' => 'thread',
          'searchkeys' => 
          array (
            0 => 'username',
            1 => 'uid',
            2 => 'viewnum',
            3 => 'replynum',
          ),
          'replacekeys' => 
          array (
            0 => 'author',
            1 => 'authorid',
            2 => 'views',
            3 => 'replies',
          ),
        ),
        'group_thread' => 
        array (
          'name' => '群组帖子',
          'script' => 'groupthread',
          'searchkeys' => 
          array (
            0 => 'username',
            1 => 'uid',
            2 => 'viewnum',
            3 => 'replynum',
          ),
          'replacekeys' => 
          array (
            0 => 'author',
            1 => 'authorid',
            2 => 'views',
            3 => 'replies',
          ),
        ),
        'portal_article' => 
        array (
          'name' => '文章模块',
          'script' => 'article',
          'searchkeys' => 
          array (
            0 => 'replynum',
          ),
          'replacekeys' => 
          array (
            0 => 'commentnum',
          ),
        ),
      ),
    ),
  ),
);
