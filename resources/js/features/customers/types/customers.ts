import { type ContactPlatform } from "@/features/contact-platforms/types/contactPlatforms";

export interface Customer {
    id: number,
    full_name: string,
    identification: string,
    email: string,
    city: string,
    phone: string,
    primary_contact_platform?: CustomerContact | null,
}

export interface CustomerContact {
    id: number,
    contact_identifier: string,
    is_primary: boolean,
    customer_id: number,
    platform_id: number,
    contact_platform: ContactPlatform,
}