class PostTag < ApplicationRecord
  has_many :posts, :through => :post_tag_links
  has_many :post_tag_links, dependent: :destroy

  belongs_to :post_type

  # Use slug instead of ID for pretty urls
  validates :slug, uniqueness: { scope: :post_type }
  validates_format_of :slug, :without => /\A\d/

  def to_param
    slug
  end

  def self.find(input)
    if input.is_a?(String)
      find_by_slug(input)
    else
      super
    end
  end
end
