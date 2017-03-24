class CreatePosts < ActiveRecord::Migration[5.0]
  def change
    create_table :posts do |t|
      t.string :title
      t.string :slug, :null => false
      t.index :slug
      t.text :text
      t.text :excrept
      t.text :extra_css
      t.text :extra_js
      t.integer :author
      t.integer :template
      t.integer :microdata
      t.integer :custom_meta
      t.integer :rights
      t.boolean :comments
      t.integer :status
      t.integer :locale
      t.belongs_to :post_type, index: true, :null => false

      t.timestamps
    end
    add_index :posts, [:slug, :post_type_id], unique: true

    create_table :post_types do |t|
      t.string :name
      t.string :slug, :null => false
      t.index :slug, unique: true
      t.integer :template
      t.integer :rights
      t.integer :locale

      t.timestamps
    end

    create_table :post_categories do |t|
      t.string :name
      t.string :slug, :null => false
      t.index :slug
      t.string :image
      t.integer :parent
      t.integer :template
      t.integer :locale
      t.integer :rights
      t.belongs_to :post_type, index: true, :null => false

      t.timestamps
    end
    add_index :post_categories, [:slug, :post_type_id], unique: true
    PostCategory.create :name => 'Uncategorized', :slug => 'uncategorized', :post_type_id => 1

    create_table :post_category_links do |t|
      t.belongs_to :post, index: true, :null => false
      t.belongs_to :post_category, index: true, :null => false

      t.timestamps
    end

    create_table :post_tags do |t|
      t.string :name
      t.string :slug, :null => false
      t.index :slug
      t.integer :locale
      t.belongs_to :post_type, index: true, :null => false

      t.timestamps
    end
    add_index :post_tags, [:slug, :post_type_id], unique: true

    create_table :post_tag_links do |t|
      t.belongs_to :post, index: true, :null => false
      t.belongs_to :post_tag, index: true, :null => false

      t.timestamps
    end
  end
end
