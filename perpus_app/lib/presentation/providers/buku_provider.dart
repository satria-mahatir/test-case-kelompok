import 'package:flutter/material.dart';
import 'package:perpus_app/data/models/buku_model.dart';
import 'package:perpus_app/data/services/buku_service.dart';

class BukuProvider extends ChangeNotifier {
  final BukuService _service = BukuService();
  
  List<BukuModel> _bukus = [];
  BukuModel? _selectedBuku;
  bool _isLoading = false;
  bool _isLoadingMore = false;
  String? _error;
  Map<String, dynamic> _pagination = {};

  int _currentPage = 1;
  int _lastPage = 1;
  int _total = 0;
  String? _currentSearch;
  int? _currentKategoriId;

  List<BukuModel> get bukus => _bukus;
  BukuModel? get selectedBuku => _selectedBuku;
  bool get isLoading => _isLoading;
  bool get isLoadingMore => _isLoadingMore;
  String? get error => _error;
  Map<String, dynamic> get pagination => _pagination;
  bool get hasMore => _currentPage < _lastPage;
  int get total => _total;

  Future<void> fetchBukus({String? search, int? kategoriId, int page = 1}) async {
    _isLoading = true;
    _error = null;
    _currentPage = page;
    _currentSearch = search;
    _currentKategoriId = kategoriId;
    notifyListeners();

    try {
      final result = await _service.getAll(search: search, kategoriId: kategoriId, page: page);
      _bukus = List<BukuModel>.from(result['list']);
      _pagination = result['pagination'] ?? {};
      _currentPage = _pagination['current_page'] ?? 1;
      _lastPage = _pagination['last_page'] ?? 1;
      _total = _pagination['total'] ?? _bukus.length;
    } catch (e) {
      _error = e.toString().replaceFirst('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> loadMoreBukus() async {
    if (_isLoadingMore || !hasMore) return;
    _isLoadingMore = true;
    notifyListeners();

    try {
      final nextPage = _currentPage + 1;
      final result = await _service.getAll(search: _currentSearch, kategoriId: _currentKategoriId, page: nextPage);
      final newItems = List<BukuModel>.from(result['list']);
      _bukus.addAll(newItems);
      _pagination = result['pagination'] ?? {};
      _currentPage = _pagination['current_page'] ?? nextPage;
      _lastPage = _pagination['last_page'] ?? _lastPage;
      _total = _pagination['total'] ?? _bukus.length;
    } catch (e) {
      _error = e.toString().replaceFirst('Exception: ', '');
    } finally {
      _isLoadingMore = false;
      notifyListeners();
    }
  }

  Future<void> fetchBukuById(int id) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      _selectedBuku = await _service.getById(id);
    } catch (e) {
      _error = e.toString().replaceFirst('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> createBuku(Map<String, dynamic> data) async {
    try {
      await _service.create(
        judul: data['judul'],
        kategoriId: data['kategori_id'],
        penulisId: data['penulis_id'],
        penerbitId: data['penerbit_id'],
        isbn: data['isbn'],
        tahunTerbit: int.tryParse(data['tahun_terbit'].toString()),
        stok: int.parse(data['stok'].toString()),
        deskripsi: data['deskripsi'],
      );
      await fetchBukus();
      return true;
    } catch (e) {
      _error = e.toString().replaceFirst('Exception: ', '');
      notifyListeners();
      return false;
    }
  }

  Future<bool> updateBuku(int id, Map<String, dynamic> data) async {
    try {
      await _service.update(
        id,
        judul: data['judul'],
        kategoriId: data['kategori_id'],
        penulisId: data['penulis_id'],
        penerbitId: data['penerbit_id'],
        isbn: data['isbn'],
        tahunTerbit: int.tryParse(data['tahun_terbit'].toString()),
        stok: int.parse(data['stok'].toString()),
        deskripsi: data['deskripsi'],
      );
      await fetchBukus();
      if (_selectedBuku?.id == id) {
        await fetchBukuById(id);
      }
      return true;
    } catch (e) {
      _error = e.toString().replaceFirst('Exception: ', '');
      notifyListeners();
      return false;
    }
  }

  Future<bool> deleteBuku(int id) async {
    try {
      await _service.delete(id);
      await fetchBukus();
      return true;
    } catch (e) {
      _error = e.toString().replaceFirst('Exception: ', '');
      notifyListeners();
      return false;
    }
  }
}
