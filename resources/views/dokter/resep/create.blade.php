@extends('layouts.app')


    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">

@section('content')
<div class="container">
    <h2>Tambah Resep</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('resep.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nama_pasien">Nama Pasien</label>
            <input type="text" name="nama_pasien" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="tanggal_resep">Tanggal Resep</label>
            <input type="date" name="tanggal_resep" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="keterangan_sakit">Keterangan Sakit</label>
            <input type="text" name="keterangan_sakit" class="form-control" required>
        </div>

        <h4>Daftar Obat</h4>
        <table class="table" id="obat-table">
            <thead>
                <tr>
                    <th>Obat</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="id_obat[]" class="form-control" required>
                            <option value="">-- Pilih Obat --</option>
                            @foreach ($obat as $o)
                                <option value="{{ $o->id_obat }}">{{ $o->nama_obat }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="jumlah[]" class="form-control" required>
                    </td>
                    <td>
                        <input type="text" name="keterangan[]" class="form-control" placeholder="Contoh: 3x1 sesudah makan">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" class="btn btn-secondary" id="add-row">Tambah Obat</button>
        <br><br>
        <button type="submit" class="btn btn-success">Simpan Resep</button>
    </form>
</div>

<script>
    document.getElementById('add-row').addEventListener('click', function () {
        const tableBody = document.querySelector('#obat-table tbody');
        const newRow = document.createElement('tr');

        newRow.innerHTML = `
            <td>
                <select name="id_obat[]" class="form-control" required>
                    <option value="">-- Pilih Obat --</option>
                    @foreach ($obat as $o)
                        <option value="{{ $o->id_obat }}">{{ $o->nama_obat }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="jumlah[]" class="form-control" required>
            </td>
            <td>
                <input type="text" name="keterangan[]" class="form-control" placeholder="Contoh: 3x1 sesudah makan">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
            </td>
        `;
        tableBody.appendChild(newRow);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
</script>
@endsection
