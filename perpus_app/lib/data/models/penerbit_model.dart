class PenerbitModel {
  final int id;
  final String namaPenerbit;
  final String? kota;
  final String? createdAt;
  final String? updatedAt;

  PenerbitModel({
    required this.id,
    required this.namaPenerbit,
    this.kota,
    this.createdAt,
    this.updatedAt,
  });

  factory PenerbitModel.fromJson(Map<String, dynamic> json) {
    return PenerbitModel(
      id: json['id'],
      namaPenerbit: json['nama_penerbit'],
      kota: json['kota'],
      createdAt: json['created_at'],
      updatedAt: json['updated_at'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nama_penerbit': namaPenerbit,
      'kota': kota,
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }

  PenerbitModel copyWith({
    int? id,
    String? namaPenerbit,
    String? kota,
    String? createdAt,
    String? updatedAt,
  }) {
    return PenerbitModel(
      id: id ?? this.id,
      namaPenerbit: namaPenerbit ?? this.namaPenerbit,
      kota: kota ?? this.kota,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
    );
  }
}
