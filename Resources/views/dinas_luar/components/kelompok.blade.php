<form action="#" method="POST">
    @csrf
    <div class="form-group">
        <label for="nomor_surat">Nomor Surat</label>
        <input type="text" name="nomor_surat" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
</form>
