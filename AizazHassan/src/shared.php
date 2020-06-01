<?php
require "db.php";
$bookmarks = $db->query("select user.id uid, bookmark.id bid, name, title, note, created, url, category
                       from user, bookmark, share
                       where user.id = {$_SESSION["user"]["id"]}
                       and share.bookmarkId=bookmark.id and user.id=share.userId")->fetchAll(PDO::FETCH_ASSOC);

// var_dump($bookmarks);
if (count($bookmarks) == 0) {
  echo "<h4 class='center'>No new shared Bookmarks!</h4>";
  exit;
}

$categories = $db->query("select * from category where user={$_SESSION["user"]["id"]} order by name")
  ->fetchAll(PDO::FETCH_ASSOC);
?>
<table class="striped" id="main-table">
  <tr style="height:60px" class="grey lighten-5">
    <th class="title">
      <a href="">Title
      </a>
    </th>
    <th class="note">
      <a href="">Note
      </a>
    </th>
    <th class="created hide-on-med-and-down">
      <a href="">Date
      </a>
    </th>
    <th class="action">Actions</th>
  </tr>
  <?php foreach ($bookmarks as $bookmark) : ?>
    <tr id="row<?= $bookmark["bid"] ?>">
      <td><span class="truncate"><a href="<?= $bookmark['url'] ?>"><?= $bookmark['title'] ?></a></span></td>
      <td><span class="truncate"><?= $bookmark['note'] ?></span></td>
      <td class="created hide-on-med-and-down"><?php
                                                $date = new DateTime($bookmark['created']);
                                                echo $date->format("d M y");
                                                ?>
      </td>
      <td class="action">
        <a class="btn-small bms-view" href="<?= $bookmark['bid'] ?>"><i class="material-icons">visibility</i></a>
        <a class="btn-small modal-trigger" href="#add_form<?= $bookmark['bid'] ?>"><i class="material-icons">add</i></a>
      </td>
    </tr>
  <?php endforeach ?>
</table>

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

<?php foreach ($bookmarks as $bookmark) : ?>
  <div id="add_form<?= $bookmark['bid'] ?>" class="modal">
    <form action="addBM" method="post">
      <div class="modal-content">
        <h5 class="center">New Bookmark</h5>
        <input type="hidden" name="owner" value="<?= $_SESSION["user"]["id"] ?>">
        <div class="input-field">
          <input id="title" type="text" name="title" value="<?= $bookmark['title'] ?>">
          <label for="title">Title</label>
        </div>
        <div class="input-field">
          <input id="url" type="text" name="url" value="<?= $bookmark['url'] ?>">
          <label for="url">URL</label>
        </div>
        <div class="input-field">
          <textarea id="note" class="materialize-textarea" name="note"><?= $bookmark['note'] ?></textarea>
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

<?php endforeach ?>

<script>
  var instanceDetail;
  document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.modal');
    var instances = M.Modal.init(elems);
    instanceDetail = M.Modal.init(document.getElementById("bm-detail"));
  });

  $(function() {

    $('select').formSelect();

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
  });
</script>

<?php
try {
  $stmt = $db->prepare("delete from share where userId=?");
  $stmt->execute([$_SESSION["user"]["id"]]);
} catch (PDOException $ex) {
  addMessage("ERROR!");
}
?>