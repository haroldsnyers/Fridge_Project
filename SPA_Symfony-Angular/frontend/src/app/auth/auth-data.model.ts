export interface AuthSignupData {
    email: string;
    username: string;
    password: string;
    passwordConfirmation: string;
}

export interface AuthLoginData {
    email: string;
    password: string;
}
