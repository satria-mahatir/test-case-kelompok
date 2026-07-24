import '../../core/constants/api_constants.dart';
import '../models/penerbit_model.dart';
import 'api_service.dart';

class PenerbitService {
  final ApiService _apiService = ApiService();

  Future<Map<String, dynamic>> getAll({String? search, int perPage = 10, int page = 1}) async {
    String query = '?per_page=$perPage&page=$page';
    if (search != null && search.isNotEmpty) {
      query += '&search=$search';
    }
    final response = await _apiService.get('${ApiConstants.penerbits}$query');
    
    List<PenerbitModel> list = [];
    if (response['data'] != null) {
      list = (response['data'] as List).map((i) => PenerbitModel.fromJson(i)).toList();
    }
    return {
      'list': list,
      'pagination': response['pagination'] ?? {},
    };
  }

  Future<PenerbitModel> getById(int id) async {
    final response = await _apiService.get('${ApiConstants.penerbits}/$id');
    return PenerbitModel.fromJson(response['data']);
  }

  Future<PenerbitModel> create(String namaPenerbit, {String? kota}) async {
    final response = await _apiService.post(ApiConstants.penerbits, data: {
      'nama_penerbit': namaPenerbit,
      if (kota != null) 'kota': kota,
    });
    return PenerbitModel.fromJson(response['data']);
  }

  Future<PenerbitModel> update(int id, String namaPenerbit, {String? kota}) async {
    final response = await _apiService.put('${ApiConstants.penerbits}/$id', data: {
      'nama_penerbit': namaPenerbit,
      if (kota != null) 'kota': kota,
    });
    return PenerbitModel.fromJson(response['data']);
  }

  Future<bool> delete(int id) async {
    await _apiService.delete('${ApiConstants.penerbits}/$id');
    return true;
  }
}
