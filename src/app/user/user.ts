import { Screen } from './screen';

export interface User {
    id: number;
    name: string;
    cpf: string;
    phone: string;
    email: string;
    profileId: number;
    screens: Screen[];
}
