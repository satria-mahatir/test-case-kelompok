import 'package:flutter/material.dart';
import 'package:perpus_app/data/models/kategori_model.dart';
import 'package:perpus_app/data/services/kategori_service.dart';

class KategoriProvider extends ChangeNotifier {
  final KategoriService _service = KategoriService();
  
  List<KategoriModel> _kategoris = [];
  bool _isLoading = false;
  String? _error;

  List<KategoriModel> get kategoris => _kategoris;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> fetchKategoris({String? search}) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final result = await _service.getAll(search: search);
      _kategoris = result['list'] as List<KategoriModel>;
    } catch (e) {
      _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> createKategori(String name) async {
    try {
      await _service.create(name);
      await fetchKategoris();
      return true;
    } catch (e) {
      _error = e.toString();
      notifyListeners();
      return false;
    }
  }

  Future<bool> updateKategori(int id, String name) async {
    try {
      await _service.update(id, name);
      await fetchKategoris();
      return true;
    } catch (e) {
      _error = e.toString();
      notifyListeners();
      return false;
    }
  }

  Future<bool> deleteKategori(int id) async {
    try {
      await _service.delete(id);
      await fetchKategoris();
      return true;
    } catch (e) {
      _error = e.toString();
      notifyListeners();
      return false;
    }
  }
}
