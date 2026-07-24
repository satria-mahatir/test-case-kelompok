import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:perpus_app/data/models/buku_model.dart';
import 'package:perpus_app/presentation/providers/buku_provider.dart';
import 'package:perpus_app/presentation/providers/kategori_provider.dart';
import 'package:perpus_app/presentation/providers/penulis_provider.dart';
import 'package:perpus_app/presentation/providers/penerbit_provider.dart';

class BukuFormScreen extends StatefulWidget {
  final BukuModel? buku;

  const BukuFormScreen({super.key, this.buku});

  @override
  State<BukuFormScreen> createState() => _BukuFormScreenState();
}

class _BukuFormScreenState extends State<BukuFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _judulController = TextEditingController();
  final _isbnController = TextEditingController();
  final _tahunController = TextEditingController();
  final _stokController = TextEditingController();
  final _deskripsiController = TextEditingController();

  int? _selectedKategori;
  int? _selectedPenulis;
  int? _selectedPenerbit;

  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<KategoriProvider>().fetchKategoris();
      context.read<PenulisProvider>().fetchPenulis();
      context.read<PenerbitProvider>().fetchPenerbit();
    });

    if (widget.buku != null) {
      _judulController.text = widget.buku!.judul;
      _isbnController.text = widget.buku!.isbn ?? '';
      _tahunController.text = widget.buku!.tahunTerbit?.toString() ?? '';
      _stokController.text = widget.buku!.stok.toString();
      _deskripsiController.text = widget.buku!.deskripsi ?? '';
      _selectedKategori = widget.buku!.kategori?.id;
      _selectedPenulis = widget.buku!.penulis?.id;
      _selectedPenerbit = widget.buku!.penerbit?.id;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0D0D1A),
      appBar: AppBar(
        backgroundColor: const Color(0xFF1A1A2E),
        title: Text(widget.buku == null ? 'Tambah Buku' : 'Edit Buku', style: const TextStyle(fontFamily: 'Poppins')),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              _buildTextField('Judul Buku', _judulController, isRequired: true),
              const SizedBox(height: 16),
              _buildTextField('ISBN', _isbnController),
              const SizedBox(height: 16),
              Row(
                children: [
                  Expanded(child: _buildTextField('Tahun Terbit', _tahunController, isNumber: true)),
                  const SizedBox(width: 16),
                  Expanded(child: _buildTextField('Stok', _stokController, isNumber: true, isRequired: true)),
                ],
              ),
              const SizedBox(height: 16),
              _buildDropdownKategori(),
              const SizedBox(height: 16),
              _buildDropdownPenulis(),
              const SizedBox(height: 16),
              _buildDropdownPenerbit(),
              const SizedBox(height: 16),
              _buildTextField('Deskripsi', _deskripsiController, maxLines: 4),
              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF8E2DE2),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: _isSaving ? null : _saveBuku,
                  child: _isSaving
                      ? const CircularProgressIndicator(color: Colors.white)
                      : const Text('Simpan', style: TextStyle(fontFamily: 'Poppins', fontSize: 16, fontWeight: FontWeight.bold)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTextField(String label, TextEditingController controller, {bool isRequired = false, bool isNumber = false, int maxLines = 1}) {
    return TextFormField(
      controller: controller,
      style: const TextStyle(color: Colors.white, fontFamily: 'Poppins'),
      keyboardType: isNumber ? TextInputType.number : TextInputType.text,
      maxLines: maxLines,
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(color: Colors.white54),
        filled: true,
        fillColor: const Color(0xFF1A1A2E),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
      ),
      validator: (val) {
        if (isRequired && (val == null || val.isEmpty)) return '$label wajib diisi';
        return null;
      },
    );
  }

  Widget _buildDropdownKategori() {
    return Consumer<KategoriProvider>(
      builder: (context, provider, _) {
        return DropdownButtonFormField<int>(
          initialValue: _selectedKategori,
          dropdownColor: const Color(0xFF1A1A2E),
          style: const TextStyle(color: Colors.white, fontFamily: 'Poppins'),
          decoration: InputDecoration(
            labelText: 'Kategori',
            labelStyle: const TextStyle(color: Colors.white54),
            filled: true,
            fillColor: const Color(0xFF1A1A2E),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
          ),
          items: provider.kategoris.map((k) {
            return DropdownMenuItem<int>(
              value: k.id,
              child: Text(k.namaKategori, style: const TextStyle(color: Colors.white)),
            );
          }).toList(),
          onChanged: (val) => setState(() => _selectedKategori = val),
          validator: (val) => val == null ? 'Kategori wajib dipilih' : null,
        );
      },
    );
  }
  
  Widget _buildDropdownPenulis() {
    return Consumer<PenulisProvider>(
      builder: (context, provider, _) {
        return DropdownButtonFormField<int>(
          initialValue: _selectedPenulis,
          dropdownColor: const Color(0xFF1A1A2E),
          style: const TextStyle(color: Colors.white, fontFamily: 'Poppins'),
          decoration: InputDecoration(
            labelText: 'Penulis',
            labelStyle: const TextStyle(color: Colors.white54),
            filled: true,
            fillColor: const Color(0xFF1A1A2E),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
          ),
          items: provider.penulisList.map((p) {
            return DropdownMenuItem<int>(
              value: p.id,
              child: Text(p.namaPenulis, style: const TextStyle(color: Colors.white)),
            );
          }).toList(),
          onChanged: (val) => setState(() => _selectedPenulis = val),
          validator: (val) => val == null ? 'Penulis wajib dipilih' : null,
        );
      },
    );
  }

  Widget _buildDropdownPenerbit() {
    return Consumer<PenerbitProvider>(
      builder: (context, provider, _) {
        return DropdownButtonFormField<int>(
          initialValue: _selectedPenerbit,
          dropdownColor: const Color(0xFF1A1A2E),
          style: const TextStyle(color: Colors.white, fontFamily: 'Poppins'),
          decoration: InputDecoration(
            labelText: 'Penerbit',
            labelStyle: const TextStyle(color: Colors.white54),
            filled: true,
            fillColor: const Color(0xFF1A1A2E),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
          ),
          items: provider.penerbitList.map((p) {
            return DropdownMenuItem<int>(
              value: p.id,
              child: Text(p.namaPenerbit, style: const TextStyle(color: Colors.white)),
            );
          }).toList(),
          onChanged: (val) => setState(() => _selectedPenerbit = val),
          validator: (val) => val == null ? 'Penerbit wajib dipilih' : null,
        );
      },
    );
  }

  Future<void> _saveBuku() async {
    if (!_formKey.currentState!.validate()) return;
    
    setState(() => _isSaving = true);
    
    final messenger = ScaffoldMessenger.of(context);
    final navigator = Navigator.of(context);
    final provider = context.read<BukuProvider>();

    final data = {
      'judul': _judulController.text,
      'isbn': _isbnController.text,
      'tahun_terbit': _tahunController.text,
      'stok': _stokController.text,
      'deskripsi': _deskripsiController.text,
      'kategori_id': _selectedKategori,
      'penulis_id': _selectedPenulis,
      'penerbit_id': _selectedPenerbit,
    };

    bool success;
    if (widget.buku == null) {
      success = await provider.createBuku(data);
    } else {
      success = await provider.updateBuku(widget.buku!.id, data);
    }

    if (!mounted) return;
    setState(() => _isSaving = false);

    if (success) {
      messenger.showSnackBar(const SnackBar(content: Text('Berhasil menyimpan buku')));
      navigator.pop();
    } else {
      messenger.showSnackBar(SnackBar(content: Text(provider.error ?? 'Gagal menyimpan')));
    }
  }
}
