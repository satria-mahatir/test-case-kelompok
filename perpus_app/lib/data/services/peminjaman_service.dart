import '../../core/constants/api_constants.dart';
import '../models/peminjaman_model.dart';
import 'api_service.dart';

class PeminjamanService {
  final ApiService _apiService = ApiService();

  Future<Map<String, dynamic>> getAll({String? search, String? status, int perPage = 10, int page = 1}) async {
    String query = '?per_page=$perPage&page=$page';
    if (search != null && search.isNotEmpty) {
      query += '&search=$search';
    }
    if (status != null && status.isNotEmpty) {
      query += '&status=$status';
    }
    final response = await _apiService.get('${ApiConstants.peminjaman}$query');
    
    List<PeminjamanModel> list = [];
    if (response['data'] != null) {
      list = (response['data'] as List).map((i) => PeminjamanModel.fromJson(i)).toList();
    }
    return {
      'list': list,
      'pagination': response['pagination'] ?? {},
    };
  }

  Future<PeminjamanModel> getById(int id) async {
    final response = await _apiService.get('${ApiConstants.peminjaman}/$id');
    return PeminjamanModel.fromJson(response['data']);
  }

  Future<PeminjamanModel> create({
    required int bukuId,
    required String namaPeminjam,
    required String nis,
    required String tanggalPinjam,
    required String tanggalKembaliRencana,
  }) async {
    final response = await _apiService.post(ApiConstants.peminjaman, data: {
      'buku_id': bukuId,
      'nama_peminjam': namaPeminjam,
      'nis': nis,
      'tanggal_pinjam': tanggalPinjam,
      'tanggal_kembali_rencana': tanggalKembaliRencana,
    });
    return PeminjamanModel.fromJson(response['data']);
  }

  Future<PeminjamanModel> kembalikan(int id) async {
    final response = await _apiService.patch('${ApiConstants.peminjaman}/$id/kembalikan');
    return PeminjamanModel.fromJson(response['data']);
  }

  Future<bool> delete(int id) async {
    await _apiService.delete('${ApiConstants.peminjaman}/$id');
    return true;
  }
}
