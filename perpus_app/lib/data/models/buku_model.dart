import 'kategori_model.dart';
import 'penulis_model.dart';
import 'penerbit_model.dart';

class BukuModel {
  final int id;
  final String judul;
  final String? isbn;
  final int? tahunTerbit;
  final int stok;
  final String? deskripsi;
  final String? cover;
  final KategoriModel? kategori;
  final PenulisModel? penulis;
  final PenerbitModel? penerbit;

  BukuModel({
    required this.id,
    required this.judul,
    this.isbn,
    this.tahunTerbit,
    required this.stok,
    this.deskripsi,
    this.cover,
    this.kategori,
    this.penulis,
    this.penerbit,
  });

  factory BukuModel.fromJson(Map<String, dynamic> json) {
    return BukuModel(
      id: json['id'],
      judul: json['judul'],
      isbn: json['isbn'],
      tahunTerbit: json['tahun_terbit'] != null ? int.tryParse(json['tahun_terbit'].toString()) : null,
      stok: json['stok'] != null ? int.tryParse(json['stok'].toString()) ?? 0 : 0,
      deskripsi: json['deskripsi'],
      cover: json['cover'],
      kategori: json['kategori'] != null ? KategoriModel.fromJson(json['kategori']) : null,
      penulis: json['penulis'] != null ? PenulisModel.fromJson(json['penulis']) : null,
      penerbit: json['penerbit'] != null ? PenerbitModel.fromJson(json['penerbit']) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'judul': judul,
      'isbn': isbn,
      'tahun_terbit': tahunTerbit,
      'stok': stok,
      'deskripsi': deskripsi,
      'cover': cover,
      'kategori': kategori?.toJson(),
      'penulis': penulis?.toJson(),
      'penerbit': penerbit?.toJson(),
    };
  }
}
