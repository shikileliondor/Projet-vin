import { Form, Head } from '@inertiajs/react';
import InputError from '@/components/input-error';
import { PageHeader } from '@/components/page-header';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { index, store, update } from '@/routes/users';

type AppUser = {
    id: number;
    name: string;
    email: string;
    role: string;
    role_label: string;
    is_active: boolean;
};
type Role = { value: string; label: string };

export default function UsersIndex({
    users,
    roles,
}: {
    users: AppUser[];
    roles: Role[];
}) {
    return (
        <>
            <Head title="Utilisateurs" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Utilisateurs"
                    description="Gérez les accès, rôles et codes PIN."
                />
                <Card>
                    <CardHeader>
                        <CardTitle>Ajouter un utilisateur</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...store.form()}
                            resetOnSuccess
                            className="grid gap-4 md:grid-cols-2 xl:grid-cols-5"
                        >
                            {({ errors, processing }) => (
                                <>
                                    <Field label="Nom *" error={errors.name}>
                                        <Input
                                            name="name"
                                            required
                                            className="h-11"
                                        />
                                    </Field>
                                    <Field
                                        label="E-mail *"
                                        error={errors.email}
                                    >
                                        <Input
                                            name="email"
                                            type="email"
                                            required
                                            className="h-11"
                                        />
                                    </Field>
                                    <Field label="Rôle *" error={errors.role}>
                                        <select
                                            name="role"
                                            defaultValue="seller"
                                            className="border-input bg-background h-11 rounded-md border px-3"
                                        >
                                            {roles.map((role) => (
                                                <option
                                                    key={role.value}
                                                    value={role.value}
                                                >
                                                    {role.label}
                                                </option>
                                            ))}
                                        </select>
                                    </Field>
                                    <Field
                                        label="PIN à 4 chiffres *"
                                        error={errors.pin}
                                    >
                                        <Input
                                            name="pin"
                                            type="password"
                                            inputMode="numeric"
                                            pattern="[0-9]{4}"
                                            maxLength={4}
                                            required
                                            className="h-11"
                                        />
                                    </Field>
                                    <div className="flex items-end">
                                        <Button
                                            type="submit"
                                            className="h-11 w-full"
                                            disabled={processing}
                                        >
                                            {processing && <Spinner />}Ajouter
                                        </Button>
                                    </div>
                                </>
                            )}
                        </Form>
                    </CardContent>
                </Card>
                <div className="grid gap-4 lg:grid-cols-2">
                    {users.map((user) => (
                        <Card
                            key={user.id}
                            className={!user.is_active ? 'opacity-60' : ''}
                        >
                            <CardHeader className="flex-row items-center justify-between">
                                <CardTitle>{user.name}</CardTitle>
                                <Badge
                                    variant={
                                        user.is_active ? 'default' : 'secondary'
                                    }
                                >
                                    {user.is_active
                                        ? user.role_label
                                        : 'Inactif'}
                                </Badge>
                            </CardHeader>
                            <CardContent>
                                <Form
                                    {...update.form(user.id)}
                                    className="grid gap-4 sm:grid-cols-2"
                                >
                                    {({ errors, processing }) => (
                                        <>
                                            <Field
                                                label="Nom"
                                                error={errors.name}
                                            >
                                                <Input
                                                    name="name"
                                                    defaultValue={user.name}
                                                    required
                                                />
                                            </Field>
                                            <Field
                                                label="E-mail"
                                                error={errors.email}
                                            >
                                                <Input
                                                    name="email"
                                                    type="email"
                                                    defaultValue={user.email}
                                                    required
                                                />
                                            </Field>
                                            <Field
                                                label="Rôle"
                                                error={errors.role}
                                            >
                                                <select
                                                    name="role"
                                                    defaultValue={user.role}
                                                    className="border-input bg-background h-9 rounded-md border px-3"
                                                >
                                                    {roles.map((role) => (
                                                        <option
                                                            key={role.value}
                                                            value={role.value}
                                                        >
                                                            {role.label}
                                                        </option>
                                                    ))}
                                                </select>
                                            </Field>
                                            <Field
                                                label="Nouveau PIN (facultatif)"
                                                error={errors.pin}
                                            >
                                                <Input
                                                    name="pin"
                                                    type="password"
                                                    inputMode="numeric"
                                                    pattern="[0-9]{4}"
                                                    maxLength={4}
                                                    placeholder="••••"
                                                />
                                            </Field>
                                            <label className="flex items-center gap-3">
                                                <input
                                                    type="hidden"
                                                    name="is_active"
                                                    value="0"
                                                />
                                                <input
                                                    type="checkbox"
                                                    name="is_active"
                                                    value="1"
                                                    defaultChecked={
                                                        user.is_active
                                                    }
                                                    className="size-5"
                                                />
                                                Compte actif
                                            </label>
                                            <div className="flex justify-end">
                                                <Button
                                                    type="submit"
                                                    variant="outline"
                                                    disabled={processing}
                                                >
                                                    {processing && <Spinner />}
                                                    Enregistrer
                                                </Button>
                                            </div>
                                        </>
                                    )}
                                </Form>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            </div>
        </>
    );
}

function Field({
    label,
    error,
    children,
}: {
    label: string;
    error?: string;
    children: React.ReactNode;
}) {
    return (
        <div className="grid gap-2">
            <Label>{label}</Label>
            {children}
            <InputError message={error} />
        </div>
    );
}
UsersIndex.layout = { breadcrumbs: [{ title: 'Utilisateurs', href: index() }] };
