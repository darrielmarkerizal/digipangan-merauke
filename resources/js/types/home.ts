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
  regions: RegionCard[]
}
