export interface Customer {
    id: number,
    full_name: string,
    email: string,
    city: string,
    phone: string,
    primary_contact_platform?: CustomerContact | null,
}

export interface ContactPlatform {
    id: number,
    name: string,
    slug: string,
}

export interface CustomerContact {
    id: number,
    contact_identifier: string,
    is_primary: boolean,
    customer_id: number,
    platform_id: number,
    contact_platform: ContactPlatform,
}