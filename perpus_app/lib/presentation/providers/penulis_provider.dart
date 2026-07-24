import 'package:flutter/material.dart';
import 'package:perpus_app/data/models/penulis_model.dart';
import 'package:perpus_app/data/services/penulis_service.dart';

class PenulisProvider extends ChangeNotifier {
  final PenulisService _service = PenulisService();
  
  List<PenulisModel> _penulisList = [];
  bool _isLoading = false;
  String? _error;

  List<PenulisModel> get penulisList => _penulisList;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> fetchPenulis({String? search}) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final result = await _service.getAll(search: search);
      _penulisList = result['list'] as List<PenulisModel>;
    } catch (e) {
      _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> createPenulis(String nama, String email) async {
    try {
      await _service.create(nama, email: email);
      await fetchPenulis();
      return true;
    } catch (e) {
      _error = e.toString();
      notifyListeners();
      return false;
    }
  }

  Future<bool> updatePenulis(int id, String nama, String email) async {
    try {
      await _service.update(id, nama, email: email);
      await fetchPenulis();
      return true;
    } catch (e) {
      _error = e.toString();
      notifyListeners();
      return false;
    }
  }

  Future<bool> deletePenulis(int id) async {
    try {
      await _service.delete(id);
      await fetchPenulis();
      return true;
    } catch (e) {
      _error = e.toString();
      notifyListeners();
      return false;
    }
  }
}
