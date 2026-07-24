import '../../core/constants/api_constants.dart';
import '../models/kategori_model.dart';
import 'api_service.dart';

class KategoriService {
  final ApiService _apiService = ApiService();

  Future<Map<String, dynamic>> getAll({String? search, int perPage = 10, int page = 1}) async {
    String query = '?per_page=$perPage&page=$page';
    if (search != null && search.isNotEmpty) {
      query += '&search=$search';
    }
    final response = await _apiService.get('${ApiConstants.kategoris}$query');
    
    List<KategoriModel> list = [];
    if (response['data'] != null) {
      list = (response['data'] as List).map((i) => KategoriModel.fromJson(i)).toList();
    }
    return {
      'list': list,
      'pagination': response['pagination'] ?? {},
    };
  }

  Future<KategoriModel> getById(int id) async {
    final response = await _apiService.get('${ApiConstants.kategoris}/$id');
    return KategoriModel.fromJson(response['data']);
  }

  Future<KategoriModel> create(String namaKategori) async {
    final response = await _apiService.post(ApiConstants.kategoris, data: {
      'nama_kategori': namaKategori,
    });
    return KategoriModel.fromJson(response['data']);
  }

  Future<KategoriModel> update(int id, String namaKategori) async {
    final response = await _apiService.put('${ApiConstants.kategoris}/$id', data: {
      'nama_kategori': namaKategori,
    });
    return KategoriModel.fromJson(response['data']);
  }

  Future<bool> delete(int id) async {
    await _apiService.delete('${ApiConstants.kategoris}/$id');
    return true;
  }
}
