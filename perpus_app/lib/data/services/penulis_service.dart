import '../../core/constants/api_constants.dart';
import '../models/penulis_model.dart';
import 'api_service.dart';

class PenulisService {
  final ApiService _apiService = ApiService();

  Future<Map<String, dynamic>> getAll({String? search, int perPage = 10, int page = 1}) async {
    String query = '?per_page=$perPage&page=$page';
    if (search != null && search.isNotEmpty) {
      query += '&search=$search';
    }
    final response = await _apiService.get('${ApiConstants.penulis}$query');
    
    List<PenulisModel> list = [];
    if (response['data'] != null) {
      list = (response['data'] as List).map((i) => PenulisModel.fromJson(i)).toList();
    }
    return {
      'list': list,
      'pagination': response['pagination'] ?? {},
    };
  }

  Future<PenulisModel> getById(int id) async {
    final response = await _apiService.get('${ApiConstants.penulis}/$id');
    return PenulisModel.fromJson(response['data']);
  }

  Future<PenulisModel> create(String namaPenulis, {String? email}) async {
    final response = await _apiService.post(ApiConstants.penulis, data: {
      'nama_penulis': namaPenulis,
      if (email != null) 'email': email,
    });
    return PenulisModel.fromJson(response['data']);
  }

  Future<PenulisModel> update(int id, String namaPenulis, {String? email}) async {
    final response = await _apiService.put('${ApiConstants.penulis}/$id', data: {
      'nama_penulis': namaPenulis,
      if (email != null) 'email': email,
    });
    return PenulisModel.fromJson(response['data']);
  }

  Future<bool> delete(int id) async {
    await _apiService.delete('${ApiConstants.penulis}/$id');
    return true;
  }
}
