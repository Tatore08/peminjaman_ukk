class AddFieldsToAlatAndPengembalian < ActiveRecord::Migration[6.0]
  def change
    add_column :alat, :harga_beli, :decimal
    rename_column :alat, :total_denda, :denda_keterlambatan
    add_column :pengembalian, :persen_kerusakan, :decimal
    add_column :pengembalian, :denda_kerusakan, :decimal
  end
end