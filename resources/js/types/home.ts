export interface MediaUrls {
  thumb: string
  card: string
}

export interface TaxonomyRef {
  name: string
  slug: string
}

export interface ProductCard {
  name: string
  slug: string
  price: string
  stock_available: boolean
  photo: MediaUrls | null
  category?: TaxonomyRef
  region?: TaxonomyRef
  farmer?: TaxonomyRef
}

export interface FarmerCard {
  name: string
  slug: string
  land_area_ha?: string | null
  products_count: number
  photo: MediaUrls | null
  region?: { name: string; slug: string } | null
  farmer_group?: { name: string } | null
  commodities?: TaxonomyRef[]
}

export interface PostCard {
  title: string
  slug: string
  published_at: string | null
  excerpt: string
  cover: MediaUrls | null
  category?: TaxonomyRef
}

export interface RegionCard {
  name: string
  slug: string
  cover: MediaUrls | null
  villages_count: number
  farmer_groups_count: number
  products_count: number
}

export interface HomeProps {
  featuredProducts: ProductCard[]
  latestProducts: ProductCard[]
  categories?: TaxonomyRef[]
  regions: RegionCard[]
}
