<form enctype="multipart/form-data" action="/api/upload" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
    Send this file: <input name="file" type="file" />
    <input type="submit" value="Send File" />
</form>