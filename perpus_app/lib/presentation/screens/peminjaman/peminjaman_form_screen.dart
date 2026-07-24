import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:perpus_app/data/models/buku_model.dart';
import 'package:perpus_app/presentation/providers/buku_provider.dart';
import 'package:perpus_app/presentation/providers/peminjaman_provider.dart';
import 'package:intl/intl.dart';

class PeminjamanFormScreen extends StatefulWidget {
  final BukuModel? buku;

  const PeminjamanFormScreen({super.key, this.buku});

  @override
  State<PeminjamanFormScreen> createState() => _PeminjamanFormScreenState();
}

class _PeminjamanFormScreenState extends State<PeminjamanFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _namaController = TextEditingController();
  final _nisController = TextEditingController();

  int? _selectedBuku;
  DateTime _tanggalPinjam = DateTime.now();
  DateTime _tanggalKembali = DateTime.now().add(const Duration(days: 7));

  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<BukuProvider>().fetchBukus();
    });

    if (widget.buku != null) {
      _selectedBuku = widget.buku!.id;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0D0D1A),
      appBar: AppBar(
        backgroundColor: const Color(0xFF1A1A2E),
        title: const Text('Pinjam Buku', style: TextStyle(fontFamily: 'Poppins')),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              _buildDropdownBuku(),
              const SizedBox(height: 16),
              _buildTextField('Nama Peminjam', _namaController),
              const SizedBox(height: 16),
              _buildTextField('NIS / NIK', _nisController),
              const SizedBox(height: 16),
              _buildDatePicker('Tanggal Pinjam', _tanggalPinjam, (date) => setState(() => _tanggalPinjam = date)),
              const SizedBox(height: 16),
              _buildDatePicker('Tanggal Kembali', _tanggalKembali, (date) => setState(() => _tanggalKembali = date)),
              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF8E2DE2),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: _isSaving ? null : _savePeminjaman,
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

  Widget _buildTextField(String label, TextEditingController controller) {
    return TextFormField(
      controller: controller,
      style: const TextStyle(color: Colors.white, fontFamily: 'Poppins'),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(color: Colors.white54),
        filled: true,
        fillColor: const Color(0xFF1A1A2E),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
      ),
      validator: (val) => (val == null || val.isEmpty) ? '$label wajib diisi' : null,
    );
  }

  Widget _buildDropdownBuku() {
    return Consumer<BukuProvider>(
      builder: (context, provider, _) {
        return DropdownButtonFormField<int>(
          initialValue: _selectedBuku,
          dropdownColor: const Color(0xFF1A1A2E),
          style: const TextStyle(color: Colors.white, fontFamily: 'Poppins'),
          decoration: InputDecoration(
            labelText: 'Pilih Buku',
            labelStyle: const TextStyle(color: Colors.white54),
            filled: true,
            fillColor: const Color(0xFF1A1A2E),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
          ),
          items: provider.bukus.map((b) {
            final inStock = b.stok > 0;
            return DropdownMenuItem<int>(
              value: inStock ? b.id : null,
              enabled: inStock,
              child: Text(
                '${b.judul} ${inStock ? "(Stok: ${b.stok})" : "(Stok Habis)"}',
                style: TextStyle(color: inStock ? Colors.white : Colors.white38),
              ),
            );
          }).toList(),
          onChanged: widget.buku != null ? null : (val) => setState(() => _selectedBuku = val),
          validator: (val) => val == null ? 'Buku wajib dipilih (pilih buku yang memiliki stok)' : null,
        );
      },
    );
  }

  Widget _buildDatePicker(String label, DateTime date, Function(DateTime) onPicked) {
    return InkWell(
      onTap: () async {
        final picked = await showDatePicker(
          context: context,
          initialDate: date,
          firstDate: DateTime(2000),
          lastDate: DateTime(2100),
          builder: (context, child) {
            return Theme(
              data: Theme.of(context).copyWith(
                colorScheme: const ColorScheme.dark(
                  primary: Color(0xFF8E2DE2),
                  onPrimary: Colors.white,
                  surface: Color(0xFF1A1A2E),
                  onSurface: Colors.white,
                ),
              ),
              child: child!,
            );
          },
        );
        if (picked != null) onPicked(picked);
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
        decoration: BoxDecoration(
          color: const Color(0xFF1A1A2E),
          borderRadius: BorderRadius.circular(12),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(label, style: const TextStyle(color: Colors.white54, fontFamily: 'Poppins')),
            Text(DateFormat('yyyy-MM-dd').format(date), style: const TextStyle(color: Colors.white, fontFamily: 'Poppins')),
          ],
        ),
      ),
    );
  }

  Future<void> _savePeminjaman() async {
    if (!_formKey.currentState!.validate()) return;
    final messenger = ScaffoldMessenger.of(context);
    final navigator = Navigator.of(context);
    final provider = context.read<PeminjamanProvider>();
    final bukuProvider = context.read<BukuProvider>();

    if (_tanggalKembali.isBefore(_tanggalPinjam)) {
      messenger.showSnackBar(const SnackBar(content: Text('Tanggal kembali tidak valid (harus setelah tanggal pinjam)')));
      return;
    }
    
    setState(() => _isSaving = true);
    
    final success = await provider.createPeminjaman(
      bukuId: _selectedBuku!,
      namaPeminjam: _namaController.text,
      nis: _nisController.text,
      tanggalPinjam: DateFormat('yyyy-MM-dd').format(_tanggalPinjam),
      tanggalKembaliRencana: DateFormat('yyyy-MM-dd').format(_tanggalKembali),
      bukuProvider: bukuProvider,
    );

    if (!mounted) return;
    setState(() => _isSaving = false);

    if (success) {
      messenger.showSnackBar(const SnackBar(content: Text('Berhasil menyimpan peminjaman')));
      navigator.pop();
    } else {
      messenger.showSnackBar(SnackBar(content: Text(provider.error ?? 'Gagal menyimpan')));
    }
  }
}
