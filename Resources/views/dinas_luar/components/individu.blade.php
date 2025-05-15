<form action="#" method="POST">
    @csrf
    <div class="form-group row">
        <div class="col-md-6">
            <label for="nomor_surat">Nomor Surat</label>
            <input type="text" class="form-control" name="nomor_surat" required>
        </div>
        <div class="col-md-6">
            <label for="ppk_id">Pejabat Pembuat Komitmen</label>
            <select name="ppk_id" class="tomselect" required>
                <option value="">-- Pilih Pejabat --</option>
                @foreach ($pejabat as $p)
                    <option value="{{ $p->id }}">{{ $p->pegawai->gelar_dpn ? $p->pegawai->gelar_dpn . ' ' : '' }}{{ $p->pegawai->nama }}{{ $p->pegawai->gelar_blk ? ', ' . $p->pegawai->gelar_blk : '' }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-6">
            <label for="pegawai_id">Pegawai</label>
            <select name="pegawai_id" class="tomselect" required>
                <option value="">-- Pilih Pegawai --</option>
                @foreach ($pegawai as $pe)
                    <option value="{{ $pe->id }}">{{ $pe->gelar_dpn ? $pe->gelar_dpn . ' ' : '' }}{{ $pe->nama }}{{ $pe->gelar_blk ? ', ' . $pe->gelar_blk : '' }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label for="nama_kegiatan">Nama Kegiatan</label>
            <input type="text" name="nama_kegiatan" class="form-control" required>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-6">
            <label for="waktu_kegiatan">Waktu Kegiatan</label>
            <input type="text" name="waktu_kegiatan" id="waktu_kegiatan" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="tempat_kegiatan">Tempat Kegiatan</label>
            <input type="text" name="tempat_kegiatan" class="form-control" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
</form>
