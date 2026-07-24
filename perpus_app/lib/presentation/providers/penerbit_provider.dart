import 'package:flutter/material.dart';
import 'package:perpus_app/data/models/penerbit_model.dart';
import 'package:perpus_app/data/services/penerbit_service.dart';

class PenerbitProvider extends ChangeNotifier {
  final PenerbitService _service = PenerbitService();
  
  List<PenerbitModel> _penerbitList = [];
  bool _isLoading = false;
  String? _error;

  List<PenerbitModel> get penerbitList => _penerbitList;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> fetchPenerbit({String? search}) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final result = await _service.getAll(search: search);
      _penerbitList = result['list'] as List<PenerbitModel>;
    } catch (e) {
      _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> createPenerbit(String nama, String kota) async {
    try {
      await _service.create(nama, kota: kota);
      await fetchPenerbit();
      return true;
    } catch (e) {
      _error = e.toString();
      notifyListeners();
      return false;
    }
  }

  Future<bool> updatePenerbit(int id, String nama, String kota) async {
    try {
      await _service.update(id, nama, kota: kota);
      await fetchPenerbit();
      return true;
    } catch (e) {
      _error = e.toString();
      notifyListeners();
      return false;
    }
  }

  Future<bool> deletePenerbit(int id) async {
    try {
      await _service.delete(id);
      await fetchPenerbit();
      return true;
    } catch (e) {
      _error = e.toString();
      notifyListeners();
      return false;
    }
  }
}
