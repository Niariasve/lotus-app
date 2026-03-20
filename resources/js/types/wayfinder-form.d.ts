declare global {
    type WayfinderFormMethod =
        | 'get'
        | 'post'
        | 'put'
        | 'patch'
        | 'delete'
        | 'GET'
        | 'POST'
        | 'PUT'
        | 'PATCH'
        | 'DELETE';

    interface Function {
        form(...args: any[]): {
            action: string;
            method: WayfinderFormMethod;
        };
    }
}

export {};
