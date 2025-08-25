@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('content')

<div class="form-container">
    <h2 class="form-title">Tambah Transaksi</h2>

    @if ($errors->any())
        <div class="alert">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('transaksi.store') }}" method="POST" id="formTransaksi">
        @csrf

        {{-- Flex Row untuk Obat dan Jumlah --}}
        <div class="form-row" style="display: flex; gap: 10px;">
            <div class="form-group" style="flex: 1;">
                <label for="id_obat">Nama Obat</label>
                <select name="id_obat" id="id_obat" class="select2" required>
                    <option value="">-- Pilih --</option>
                    @foreach ($obat as $o)
                        <option value="{{ $o->id_obat }}" data-harga="{{ $o->harga }}">{{ $o->nama_obat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="flex: 1;">
                <label for="jumlah">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" min="1" required>
            </div>
        </div>

        {{-- Jenis Transaksi --}}
        <div class="form-group">
            <label for="jenis">Jenis Transaksi</label>
            <select name="jenis" id="jenis" required>
                <!-- <option value="">-- Pilih --</option> -->
                <!-- <option value="masuk">Masuk</option> -->
                <option value="keluar">Keluar</option>
            </select>
        </div>
<!-- 
        {{-- Tanggal Kadaluarsa (tampil jika "masuk") --}}
        <div class="form-group" id="tanggalKadaluarsaContainer" style="display: none;">
            <label for="tanggal_kadaluarsa">Tanggal Kadaluarsa</label>
            <input type="date" name="tanggal_kadaluarsa" id="tanggal_kadaluarsa">
        </div> -->

        {{-- Total Harga --}}
        <div class="form-group">
            <label for="totalHargaDisplay">Total Harga</label>
            <input type="text" id="totalHargaDisplay" readonly class="readonly">
            <input type="hidden" name="total" id="totalHarga">
        </div>

        {{-- Tanggal Transaksi --}}
        <div class="form-group">
            <label for="tanggal_transaksi">Tanggal Transaksi</label>
            <input type="date" name="tanggal_transaksi" required>
        </div>

        {{-- Submit --}}
        <div class="form-actions">
            <button type="submit" class="button-submit">Simpan</button>
        </div>
    </form>
</div>

{{-- JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    // Inisialisasi Select2
    $('#id_obat').select2({
        placeholder: "-- Pilih Obat --",
        allowClear: true,
        width: '100%'
    });

    // Hitung total harga
    function updateTotal() {
        const harga = parseInt($('#id_obat option:selected').data('harga')) || 0;
        const jumlah = parseInt($('#jumlah').val()) || 0;
        const total = harga * jumlah;

        $('#totalHarga').val(total);
        $('#totalHargaDisplay').val('Rp' + total.toLocaleString('id-ID'));
    }

    $('#id_obat, #jumlah').on('change keyup', updateTotal);

    // Toggle tanggal kadaluarsa
    $('#jenis').on('change', function () {
        const jenis = $(this).val();
        if (jenis === 'masuk') {
            $('#tanggalKadaluarsaContainer').slideDown();
            $('#tanggal_kadaluarsa').attr('required', true);
        } else {
            $('#tanggalKadaluarsaContainer').slideUp();
            $('#tanggal_kadaluarsa').removeAttr('required').val('');
        }
    });

    $('#jenis').trigger('change');
});
</script>
@endsection
