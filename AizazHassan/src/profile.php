<div class="container">
    <h1 class="center">Edit Profile</h1>
    <div style="margin:30px 45%;">
        <img style="width:150px;" class="materialboxed circle" src="<?= (is_null($_SESSION['user']['profile']) ? 'img/default.jpg' : "upload/{$_SESSION['user']['profile']}") ?>">
    </div>
    <form action="?page=updateUser" method="post" enctype="multipart/form-data">
        <div class="input-field">
            <input id="name" type="text" class="validate" name="name" value="<?= $_SESSION['user']['name'] ?>">
            <label for="name">User Name</label>
        </div>
        <div class="input-field">
            <input id="email" type="text" class="validate" name="email" value="<?= $_SESSION['user']['email'] ?>">
            <label for="email">Email</label>
        </div>
        <div class="input-field col s12">
            <input id="password" type="password" class="validate" name="password">
            <label for="password">Enter New Password</label>
        </div>
        <div class="input-field">
            <input type="text" class="datepicker" id="bday" name="bday" value="<?= (is_null($_SESSION['user']['bday']) ? "" : $_SESSION['user']['bday']) ?>">
            <label for="bday">Enter Birthday</label>
        </div>
        <div>
            <div class="file-field input-field">
                <div class="btn">
                    <span>File</span>
                    <input type="file" name="profile">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Select New Profile Picture" value="">
                </div>
            </div>
        </div>
        <div class="center">
            <button class="btn waves-effect waves-light" type="submit" name="action">Save
                <i class="material-icons right">send</i>
            </button>
        </div>
    </form>
</div>

<script>
    $(function() {
        var currYear = (new Date()).getFullYear();

        $('.datepicker').datepicker({
            minDate: new Date(currYear - 61, 11),
            maxDate: new Date(currYear - 3, 11),
            yearRange: [currYear - 60, currYear - 4],
            defaultDate: new Date(currYear - 20, 0)
        });

        $('.materialboxed').materialbox();
    });
</script>