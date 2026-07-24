import 'package:flutter/material.dart';
import 'package:perpus_app/data/models/peminjaman_model.dart';
import 'package:perpus_app/data/services/peminjaman_service.dart';
import 'package:perpus_app/presentation/providers/buku_provider.dart';

class PeminjamanProvider extends ChangeNotifier {
  final PeminjamanService _service = PeminjamanService();
  
  List<PeminjamanModel> _peminjamanList = [];
  bool _isLoading = false;
  bool _isLoadingMore = false;
  String? _error;
  Map<String, dynamic> _pagination = {};

  int _currentPage = 1;
  int _lastPage = 1;
  int _total = 0;
  String? _currentSearch;
  String? _currentStatus;

  List<PeminjamanModel> get peminjamanList => _peminjamanList;
  bool get isLoading => _isLoading;
  bool get isLoadingMore => _isLoadingMore;
  String? get error => _error;
  Map<String, dynamic> get pagination => _pagination;
  bool get hasMore => _currentPage < _lastPage;
  int get total => _total;

  Future<void> fetchPeminjaman({String? search, String? status, int page = 1}) async {
    _isLoading = true;
    _error = null;
    _currentPage = page;
    _currentSearch = search;
    _currentStatus = status;
    notifyListeners();

    try {
      final result = await _service.getAll(search: search, status: status, page: page);
      _peminjamanList = List<PeminjamanModel>.from(result['list']);
      _pagination = result['pagination'] ?? {};
      _currentPage = _pagination['current_page'] ?? 1;
      _lastPage = _pagination['last_page'] ?? 1;
      _total = _pagination['total'] ?? _peminjamanList.length;
    } catch (e) {
      _error = e.toString().replaceFirst('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> loadMorePeminjaman() async {
    if (_isLoadingMore || !hasMore) return;
    _isLoadingMore = true;
    notifyListeners();

    try {
      final nextPage = _currentPage + 1;
      final result = await _service.getAll(search: _currentSearch, status: _currentStatus, page: nextPage);
      final newItems = List<PeminjamanModel>.from(result['list']);
      _peminjamanList.addAll(newItems);
      _pagination = result['pagination'] ?? {};
      _currentPage = _pagination['current_page'] ?? nextPage;
      _lastPage = _pagination['last_page'] ?? _lastPage;
      _total = _pagination['total'] ?? _peminjamanList.length;
    } catch (e) {
      _error = e.toString().replaceFirst('Exception: ', '');
    } finally {
      _isLoadingMore = false;
      notifyListeners();
    }
  }

  Future<bool> createPeminjaman({
    required int bukuId,
    required String namaPeminjam,
    required String nis,
    required String tanggalPinjam,
    required String tanggalKembaliRencana,
    BukuProvider? bukuProvider,
  }) async {
    try {
      await _service.create(
        bukuId: bukuId,
        namaPeminjam: namaPeminjam,
        nis: nis,
        tanggalPinjam: tanggalPinjam,
        tanggalKembaliRencana: tanggalKembaliRencana,
      );
      await fetchPeminjaman(search: _currentSearch, status: _currentStatus);
      if (bukuProvider != null) {
        await bukuProvider.fetchBukus();
      }
      return true;
    } catch (e) {
      _error = e.toString().replaceFirst('Exception: ', '');
      notifyListeners();
      return false;
    }
  }

  Future<bool> kembalikanBuku(int id, {BukuProvider? bukuProvider}) async {
    try {
      await _service.kembalikan(id);
      await fetchPeminjaman(search: _currentSearch, status: _currentStatus);
      if (bukuProvider != null) {
        await bukuProvider.fetchBukus();
      }
      return true;
    } catch (e) {
      _error = e.toString().replaceFirst('Exception: ', '');
      notifyListeners();
      return false;
    }
  }

  Future<bool> deletePeminjaman(int id) async {
    try {
      await _service.delete(id);
      await fetchPeminjaman(search: _currentSearch, status: _currentStatus);
      return true;
    } catch (e) {
      _error = e.toString().replaceFirst('Exception: ', '');
      notifyListeners();
      return false;
    }
  }
}
