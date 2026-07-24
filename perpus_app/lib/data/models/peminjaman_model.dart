import 'buku_model.dart';

class PeminjamanModel {
  final int id;
  final String namaPeminjam;
  final String nis;
  final String tanggalPinjam;
  final String tanggalKembaliRencana;
  final String? tanggalPengembalian;
  final String status;
  final BukuModel? buku;

  PeminjamanModel({
    required this.id,
    required this.namaPeminjam,
    required this.nis,
    required this.tanggalPinjam,
    required this.tanggalKembaliRencana,
    this.tanggalPengembalian,
    required this.status,
    this.buku,
  });

  factory PeminjamanModel.fromJson(Map<String, dynamic> json) {
    return PeminjamanModel(
      id: json['id'],
      namaPeminjam: json['nama_peminjam'],
      nis: json['nis'],
      tanggalPinjam: json['tanggal_pinjam'],
      tanggalKembaliRencana: json['tanggal_kembali_rencana'],
      tanggalPengembalian: json['tanggal_pengembalian'],
      status: json['status'],
      buku: json['buku'] != null ? BukuModel.fromJson(json['buku']) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nama_peminjam': namaPeminjam,
      'nis': nis,
      'tanggal_pinjam': tanggalPinjam,
      'tanggal_kembali_rencana': tanggalKembaliRencana,
      'tanggal_pengembalian': tanggalPengembalian,
      'status': status,
      'buku': buku?.toJson(),
    };
  }
}
