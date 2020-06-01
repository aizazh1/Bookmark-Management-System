<?php

require "db.php";

// To remember sort between pages.
// You can use the same technique for page numbers in pagination.
//$sort = $_GET["sort"] ?? "created" ;
if (!isset($_GET["sort"])) {
  $sort = $_SESSION["sort"] ?? "created";
} else {
  $sort = $_GET["sort"];
  $_SESSION["sort"] = $sort;
}

if (!isset($_GET['cat'])) {
  $category = $_SESSION['cat'] ?? 'all';
} else {
  $category = $_GET['cat'];
  $_SESSION['cat'] = $category;
}

// $key=$_POST["search"]??"";
if (!isset($_POST["search"])) {
  $key = $_SESSION["search"] ?? "";
} else {
  $key = $_POST["search"];
  $_SESSION["search"] = $key;
}

$users = $db->query("select * from user order by name")->fetchAll(PDO::FETCH_ASSOC);

$categories = $db->query("select * from category where user={$_SESSION["user"]["id"]} order by name")
  ->fetchAll(PDO::FETCH_ASSOC);

$categoryId = 0;
foreach ($categories as $cat) {
  if ($cat['name'] == $category) {
    $categoryId = $cat["id"];
  }
}

if ($categoryId == 0) {
  $bookmarks = $db->query("select user.id uid, bookmark.id bid, name, title, note, created, url, category
                            from bookmark, user 
                            where user.id = bookmark.owner and user.id = {$_SESSION["user"]["id"]}
                            and (title like '%$key%' or note like '%$key%')
                            order by $sort desc")->fetchAll(PDO::FETCH_ASSOC);
} else {
  $bookmarks = $db->query("select user.id uid, bookmark.id bid, name, title, note, created, url,category
                            from bookmark, user 
                            where user.id = bookmark.owner and user.id = {$_SESSION["user"]["id"]} 
                            and category=$categoryId and (title like '%$key%' or note like '%$key%')
                            order by $sort desc")->fetchAll(PDO::FETCH_ASSOC);
}

$size = count($bookmarks);
$totalPage = (int) ceil($size / 10);

if (!isset($_GET['no'])) {
  $no = $_SESSION['no'] ?? 1;
} else {
  $no = $_GET['no'];
  $_SESSION['no'] = $no;
}
$no = $no > $totalPage ? 1 : $no;
$start = ($no - 1) * 10;
$end = $start + 10 > $size ? $size : $start + 10;
?>
<!-- Floating button at the bottom right -->

<div class="fixed-action-btn">
  <a class="btn-floating btn-large red modal-trigger z-depth-2" href="#add_form">
    <i class="large material-icons">add</i>
  </a>
</div>

<!-- Main Table for all bookmarks -->
<div class="row">
  <div class="col s2">
    <div class="collection">
      <a href="?cat=all" class="collection-item <?= ($category == 'all' ? "active" : "") ?>">All</a>
      <?php foreach ($categories as $cat) : ?>
        <a href="?cat=<?= $cat['name'] ?>" class="collection-item <?= ($category == $cat['name'] ? "active" : "") ?>">
          <?= $cat['name'] ?>
        </a>
      <?php endforeach ?>
    </div>
    <div class="row">
      <div class="col s2">
        <a class="btn-floating btn-small waves-effect waves-light red modal-trigger" href="#add_cat">
          <i class="material-icons">add</i>
        </a>
      </div>
      <div class="col s2">
        <a class="btn-floating btn-small waves-effect waves-light red category-del" href="?page=deleteCategory&id=<?= $categoryId ?>">
          <i class="material-icons">remove</i>
        </a>
      </div>
    </div>
  </div>
  <div class="col s10">
    <form action="" method="post">
      <div class="input-field col s6 offset-s2">
        <input type="text" class="validate" name="search" id="search" value="<?= $key ?>">
        <label for="search">Search</label>
      </div>
      <button class="btn waves-effect waves-light" type="submit" id="btnSearch" style="position:relative;top:20px;">
        Search
        <i class="material-icons right">search</i>
      </button>
    </form>
    <table class="striped" id="main-table">
      <tr style="height:60px" class="grey lighten-5">
        <th class="title" style="width:250px;">
          <a href="?sort=title">Title
            <?= $sort == "title" ? "<i class='material-icons'>arrow_drop_down</i>" : "" ?>
          </a>
        </th>
        <th class="note" style="width: 400px;">
          <a href="?sort=note">Note
            <?= $sort == "note" ? "<i class='material-icons'>arrow_drop_down</i>" : "" ?>
          </a>
        </th>
        <th class="created hide-on-med-and-down">
          <a href="?sort=created">Date
            <?= $sort == "created" ? "<i class='material-icons'>arrow_drop_down</i>" : "" ?>
          </a>
        </th>
        <th class="action">Actions</th>
      </tr>
      <?php for ($i = $start; $i < $end; $i++) : ?>
        <tr id="row<?= $bookmarks[$i]["bid"] ?>">
          <td><span class="truncate"><a href="<?= $bookmarks[$i]['url'] ?>"><?= $bookmarks[$i]['title'] ?></a></span></td>
          <td><span class="truncate"><?= $bookmarks[$i]['note'] ?></span></td>
          <td class="created hide-on-med-and-down"><?php
                                                    $date = new DateTime($bookmarks[$i]['created']);
                                                    echo $date->format("d M y");
                                                    ?>
          </td>
          <td class="action">
            <a href="<?= $bookmarks[$i]["bid"] ?>" class="bms-delete btn-small"><i class="material-icons">delete</i></a>
            <a class="btn-small bms-view" href="<?= $bookmarks[$i]['bid'] ?>"><i class="material-icons">visibility</i></a>
            <a href="#BMEdit<?= $bookmarks[$i]['bid'] ?>" class="btn-small modal-trigger"><i class="material-icons">edit</i></a>
            <a href="#BMShare<?= $bookmarks[$i]['bid'] ?>" class="btn-small modal-trigger"><i class="material-icons">share</i></a>
          </td>
        </tr>
      <?php endfor ?>
    </table>

    <?php
    echo "<ul class='pagination'>";
    if ($no == 1) {
      echo '<li class="disabled"><a href=""><i class="material-icons">chevron_left</i></a></li>';
    } else {
      echo "<li><a href='?no=" . ($no - 1) . "'><i class='material-icons'>chevron_left</i></a></li>";
    }

    for ($i = 1; $i <= $totalPage; $i++) {
      echo "<li ",
        ($no == $i ? "class='active'" : ""),
        "><a href='?no=$i'>$i</a></li>";
    }

    if ($no == $totalPage) {
      echo '<li class="disabled"><a href=""><i class="material-icons">chevron_right</i><a href=""></li>';
    } else {
      echo '<li class="waves-effect"><a href="?no=' . ($no + 1) . '"><i class="material-icons">chevron_right</i></a></li>';
    }
    echo "</ul>";
    ?>
  </div>
</div>

<!-- All modal bookmarks in detail to show after clicking view buttons -->
<div id="bm-detail" class="modal">
  <div class="modal-content">
    <table class="striped">
      <tr>
        <td>Title:</td>
        <td id="detail-title"></td>
      </tr>
      <tr>
        <td>Note:</td>
        <td id="detail-note"></td>
      </tr>
      <tr>
        <td>URL:</td>
        <td id="detail-url"></td>
      </tr>
      <tr>
        <td>Date:</td>
        <td id="detail-date"></td>
      </tr>
    </table>
  </div>
  <div class="modal-footer">
    <a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a>
  </div>
</div>



<!-- Modal Form for new Bookmark -->
<div id="add_form" class="modal">
  <form action="addBM" method="post">
    <div class="modal-content">
      <h5 class="center">New Bookmark</h5>
      <input type="hidden" name="owner" value="<?= $_SESSION["user"]["id"] ?>">
      <div class="input-field">
        <input id="title" type="text" name="title">
        <label for="title">Title</label>
      </div>
      <div class="input-field">
        <input id="url" type="text" name="url">
        <label for="url">URL</label>
      </div>
      <div class="input-field">
        <textarea id="note" class="materialize-textarea" name="note"></textarea>
        <label for="note">Notes</label>
      </div>
      <div class="input-field">
        <select name="cat">
          <option value="0" selected disabled>Select a category</option>
          <?php foreach ($categories as $cat) : ?>
            <option value="<?= $cat["id"] ?>"><?= $cat["name"] ?></option>
          <?php endforeach ?>
        </select>
        <label>Category</label>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn waves-effect waves-light" type="submit" name="action">Add
        <i class="material-icons right">send</i>
      </button>
    </div>
  </form>

</div>

<?php foreach ($bookmarks as $bm) : ?>
  <div id="BMEdit<?= $bm['bid'] ?>" class="modal">
    <form action="editBM" method="post">
      <div class="modal-content">
        <h5 class="center">Edit Bookmark</h5>
        <input type="hidden" name="id" value="<?= $bm['bid'] ?>">
        <div class="input-field">
          <input type="text" name="title" value="<?= $bm['title'] ?>">
          <label for="title">Title</label>
        </div>
        <div class="input-field">
          <input type="text" name="url" value="<?= $bm['url'] ?>">
          <label for="url">URL</label>
        </div>
        <div class="input-field">
          <textarea class="materialize-textarea" name="note"><?= $bm['note'] ?></textarea>
          <label for="note">Notes</label>
        </div>
        <div class="input-field">
          <select name="cat">
            <option value="0" <?= (is_null($bm['category']) ? 'selected' : "") ?> disabled>Select a category</option>
            <?php foreach ($categories as $cat) : ?>
              <option value="<?= $cat["id"] ?>" <?= ($cat['id'] == $bm['category'] ? 'selected' : "") ?>><?= $cat["name"] ?></option>
            <?php endforeach ?>
          </select>
          <label>Category</label>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn waves-effect waves-light" type="submit" name="action">Confirm Changes
          <i class="material-icons right">send</i>
        </button>
      </div>
    </form>

  </div>
<?php endforeach ?>

<?php foreach ($bookmarks as $bm) : ?>
  <div id="BMShare<?= $bm['bid'] ?>" class="modal">
    <form action="BMShare" method="post">
      <div class="modal-content">
        <h5 class="center">Share Bookmark</h5>
        <input type="hidden" name="bmId" value="<?= $bm['bid'] ?>">
        <div class="input-field">
          <select multiple name="userSelected[]">
            <?php foreach ($users as $user) : ?>
              <?php if ($user['id'] != $_SESSION['user']['id']) : ?>
                <option value="<?= $user['id'] ?>"><?= $user['name'] ?></option>
              <?php endif ?>
            <?php endforeach ?>
          </select>
          <label>Users</label>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn waves-effect waves-light" type="submit">Share
          <i class="material-icons right">send</i>
        </button>
      </div>
    </form>
  </div>
<?php endforeach ?>


<div id="add_cat" class="modal">
  <form action="addCat" method="post">
    <div class="modal-content">
      <input type="hidden" name="owner" value="<?= $_SESSION["user"]["id"] ?>">
      <h5 class="center">New Category</h5>
      <div class="input-field">
        <input id="name" type="text" name="name">
        <label for="name">Name</label>
      </div>
      <div class="modal-footer">
        <button class="btn waves-effect waves-light" type="submit" name="action">Add
          <i class="material-icons right">send</i>
        </button>
      </div>
  </form>

</div>

<div class="center hide" id="loader">
  <div class="preloader-wrapper small active">
    <div class="spinner-layer spinner-green-only">
      <div class="circle-clipper left">
        <div class="circle"></div>
      </div>
      <div class="gap-patch">
        <div class="circle"></div>
      </div>
      <div class="circle-clipper right">
        <div class="circle"></div>
      </div>
    </div>
  </div>
</div>

<!-- Initialization of modal elements and listboxes -->
<script>
  var instanceDetail;
  document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.modal');
    var instances = M.Modal.init(elems);
    instanceDetail = M.Modal.init(document.getElementById("bm-detail"));

    elems = document.querySelectorAll('select');
    M.FormSelect.init(elems);
  });


  $(function() {
    // page is loaded
    //alert("jquery works");

    $(".bms-delete").click(function(e) {
      e.preventDefault();
      // alert("Delete Clicked") ;
      let id = $(this).attr("href");
      //alert( id + " clicked");
      $("#loader").toggleClass("hide"); // show loader.
      $.get("delete", {
          "id": id
        },
        function(data) {
          console.log(data);
          $("#row" + id).remove(); // removes from html table.
          $("#loader").toggleClass("hide"); // hide loader.
          M.toast({
            html: 'Deleted',
            classes: 'rounded',
            displayLength: 1000
          });
        },
        "json"
      );
    });


    $(".bms-view").click(function(e) {
      e.preventDefault();
      let id = $(this).attr("href");
      console.log("bms view clicked id " + id);
      $("#loader").toggleClass("hide"); // show loader.
      $.get("getBM", {
          "id": id
        },
        function(data) {
          console.log(data);
          $("#detail-title").text(data.title);
          $("#detail-url").text(data.url);
          $("#detail-note").text(data.note);
          $("#detail-date").text(data.created);
          instanceDetail.open();
          $("#loader").toggleClass("hide"); // hide loader.
        }, "json"
      )
    });

    window.setInterval(function() {
      getNotification();
    }, 5000);

    function getNotification() {
      let id = $('#user').attr('data-target');
      // console.log("Id: "+id);
      $.get("getNotifications", {
          'id': id
        },
        function(data) {
          // console.log(data.length);
          if(data.length>0){
            $('#counter').text(data.length + " new");
          }
        }, "json"
      )
    }

  });
</script>