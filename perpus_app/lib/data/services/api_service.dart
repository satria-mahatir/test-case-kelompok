import 'dart:convert';
import 'package:http/http.dart' as http;
import '../../core/constants/api_constants.dart';

class ApiService {
  Map<String, String> get _headers => {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      };

  dynamic _processResponse(http.Response response) {
    final body = json.decode(response.body);
    if (response.statusCode >= 200 && response.statusCode < 300) {
      if (body is Map && body.containsKey('success') && body['success'] == false) {
        throw Exception(body['message'] ?? 'An error occurred');
      }
      return body;
    } else {
      String message = 'Something went wrong';
      if (body is Map) {
        if (body.containsKey('errors') && body['errors'] is Map && (body['errors'] as Map).isNotEmpty) {
          final errorsMap = body['errors'] as Map;
          final firstError = errorsMap.values.first;
          if (firstError is List && firstError.isNotEmpty) {
            message = firstError.first.toString();
          } else {
            message = firstError.toString();
          }
        } else if (body.containsKey('message')) {
          message = body['message'].toString();
        }
      }
      throw Exception(message);
    }
  }

  Future<dynamic> get(String endpoint) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConstants.baseUrl}$endpoint'),
        headers: _headers,
      );
      return _processResponse(response);
    } catch (e) {
      throw Exception(e.toString());
    }
  }

  Future<dynamic> post(String endpoint, {Map<String, dynamic>? data}) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConstants.baseUrl}$endpoint'),
        headers: _headers,
        body: data != null ? json.encode(data) : null,
      );
      return _processResponse(response);
    } catch (e) {
      throw Exception(e.toString());
    }
  }

  Future<dynamic> put(String endpoint, {Map<String, dynamic>? data}) async {
    try {
      final response = await http.put(
        Uri.parse('${ApiConstants.baseUrl}$endpoint'),
        headers: _headers,
        body: data != null ? json.encode(data) : null,
      );
      return _processResponse(response);
    } catch (e) {
      throw Exception(e.toString());
    }
  }

  Future<dynamic> patch(String endpoint, {Map<String, dynamic>? data}) async {
    try {
      final response = await http.patch(
        Uri.parse('${ApiConstants.baseUrl}$endpoint'),
        headers: _headers,
        body: data != null ? json.encode(data) : null,
      );
      return _processResponse(response);
    } catch (e) {
      throw Exception(e.toString());
    }
  }

  Future<dynamic> delete(String endpoint) async {
    try {
      final response = await http.delete(
        Uri.parse('${ApiConstants.baseUrl}$endpoint'),
        headers: _headers,
      );
      return _processResponse(response);
    } catch (e) {
      throw Exception(e.toString());
    }
  }
}
