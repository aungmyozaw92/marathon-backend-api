<form method="post" enctype="multipart/form-data">
    {{ csrf_field() }}

    <h1>Upload</h1>
    <label>
        Upload a file<br>
        <input type="file" name="file" />
    </label>

    <p><button>Submit</button></p>
</form>

<h1>Existing Files</h1>

