import { Form, Head } from '@inertiajs/react';
import InputError from '@/components/input-error';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { edit, update } from '@/routes/settings/application';

export default function ApplicationSettings({
    settings,
}: {
    settings: { business_name: string; currency: string };
}) {
    return (
        <>
            <Head title="Paramètres" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Paramètres"
                    description="Réglages généraux de WineStock."
                />
                <Card className="max-w-2xl">
                    <CardContent>
                        <Form {...update.form()} className="grid gap-5">
                            {({ errors, processing }) => (
                                <>
                                    <div className="grid gap-2">
                                        <Label htmlFor="business_name">
                                            Nom de l'établissement
                                        </Label>
                                        <Input
                                            id="business_name"
                                            name="business_name"
                                            defaultValue={
                                                settings.business_name
                                            }
                                            required
                                            className="h-11"
                                        />
                                        <InputError
                                            message={errors.business_name}
                                        />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="currency">Devise</Label>
                                        <Input
                                            id="currency"
                                            name="currency"
                                            defaultValue={settings.currency}
                                            required
                                            className="h-11"
                                        />
                                        <InputError message={errors.currency} />
                                    </div>
                                    <div className="flex justify-end">
                                        <Button
                                            type="submit"
                                            size="lg"
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
            </div>
        </>
    );
}

ApplicationSettings.layout = {
    breadcrumbs: [{ title: 'Paramètres', href: edit() }],
};
