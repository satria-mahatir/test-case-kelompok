import '../../core/constants/api_constants.dart';
import '../models/buku_model.dart';
import 'api_service.dart';

class BukuService {
  final ApiService _apiService = ApiService();

  Future<Map<String, dynamic>> getAll({String? search, int? kategoriId, int perPage = 10, int page = 1}) async {
    String query = '?per_page=$perPage&page=$page';
    if (search != null && search.isNotEmpty) {
      query += '&search=$search';
    }
    if (kategoriId != null) {
      query += '&kategori_id=$kategoriId';
    }
    final response = await _apiService.get('${ApiConstants.bukus}$query');
    
    List<BukuModel> list = [];
    if (response['data'] != null) {
      list = (response['data'] as List).map((i) => BukuModel.fromJson(i)).toList();
    }
    return {
      'list': list,
      'pagination': response['pagination'] ?? {},
    };
  }

  Future<BukuModel> getById(int id) async {
    final response = await _apiService.get('${ApiConstants.bukus}/$id');
    return BukuModel.fromJson(response['data']);
  }

  Future<BukuModel> create({
    required String judul,
    required int kategoriId,
    required int penulisId,
    required int penerbitId,
    String? isbn,
    int? tahunTerbit,
    required int stok,
    String? deskripsi,
  }) async {
    final response = await _apiService.post(ApiConstants.bukus, data: {
      'judul': judul,
      'kategori_id': kategoriId,
      'penulis_id': penulisId,
      'penerbit_id': penerbitId,
      if (isbn != null) 'isbn': isbn,
      if (tahunTerbit != null) 'tahun_terbit': tahunTerbit,
      'stok': stok,
      if (deskripsi != null) 'deskripsi': deskripsi,
    });
    return BukuModel.fromJson(response['data']);
  }

  Future<BukuModel> update(int id, {
    required String judul,
    required int kategoriId,
    required int penulisId,
    required int penerbitId,
    String? isbn,
    int? tahunTerbit,
    required int stok,
    String? deskripsi,
  }) async {
    final response = await _apiService.put('${ApiConstants.bukus}/$id', data: {
      'judul': judul,
      'kategori_id': kategoriId,
      'penulis_id': penulisId,
      'penerbit_id': penerbitId,
      if (isbn != null) 'isbn': isbn,
      if (tahunTerbit != null) 'tahun_terbit': tahunTerbit,
      'stok': stok,
      if (deskripsi != null) 'deskripsi': deskripsi,
    });
    return BukuModel.fromJson(response['data']);
  }

  Future<bool> delete(int id) async {
    await _apiService.delete('${ApiConstants.bukus}/$id');
    return true;
  }
}
