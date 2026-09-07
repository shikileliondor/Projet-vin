import { Head, router } from '@inertiajs/react';
import { Delete, Wine } from 'lucide-react';
import { useState } from 'react';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';

export default function Login({
    hasActiveUser,
    status,
}: {
    hasActiveUser: boolean;
    status?: string;
}) {
    const [pin, setPin] = useState('');
    const [processing, setProcessing] = useState(false);
    const [hasError, setHasError] = useState(false);

    const addDigit = (digit: string) => {
        if (!hasActiveUser || processing || pin.length >= 4) return;

        const nextPin = `${pin}${digit}`;

        setPin(nextPin);
        setHasError(false);

        if (nextPin.length !== 4) return;

        router.post(
            store.url(),
            { pin_user: 'winestock', password: nextPin },
            {
                preserveScroll: true,
                onStart: () => setProcessing(true),
                onError: () => {
                    setPin('');
                    setHasError(true);
                },
                onFinish: () => setProcessing(false),
            },
        );
    };

    const removeDigit = () => {
        if (processing) return;

        setPin(pin.slice(0, -1));
        setHasError(false);
    };

    return (
        <>
            <Head title="Connexion" />
            <main className="flex w-full flex-col items-center gap-8 text-stone-900">
                <header className="flex flex-col items-center gap-3 text-center">
                    <div className="flex size-14 items-center justify-center rounded-2xl border border-amber-200 bg-white text-amber-700 shadow-sm">
                        <Wine className="size-7" strokeWidth={1.8} />
                    </div>
                    <div className="grid gap-1">
                        <h1 className="text-3xl font-bold tracking-tight text-stone-950">
                            WineStock
                        </h1>
                        <p className="text-xs text-stone-500">
                            Gestion de cave professionnelle
                        </p>
                    </div>
                </header>

                {!hasActiveUser && (
                    <div className="w-full rounded-2xl border border-red-200 bg-red-50 p-4 text-center text-sm text-red-700">
                        Aucun utilisateur actif. Contactez un administrateur.
                    </div>
                )}

                <section
                    className="grid w-full gap-5"
                    aria-label="Saisie du code PIN"
                >
                    <div
                        className="flex h-6 items-center justify-center gap-3"
                        aria-label={`${pin.length} chiffre(s) saisi(s)`}
                    >
                        {[0, 1, 2, 3].map((position) => (
                            <span
                                key={position}
                                className={`size-3 rounded-full border transition-colors ${
                                    position < pin.length
                                        ? 'border-amber-500 bg-amber-500'
                                        : 'border-stone-300 bg-white'
                                }`}
                            />
                        ))}
                        {processing && (
                            <Spinner className="ml-1 text-amber-600" />
                        )}
                    </div>

                    <div className="grid grid-cols-3 gap-3">
                        {['1', '2', '3', '4', '5', '6', '7', '8', '9'].map(
                            (digit) => (
                                <PinButton
                                    key={digit}
                                    label={digit}
                                    onClick={() => addDigit(digit)}
                                    disabled={!hasActiveUser || processing}
                                />
                            ),
                        )}
                        <span aria-hidden="true" />
                        <PinButton
                            label="0"
                            onClick={() => addDigit('0')}
                            disabled={!hasActiveUser || processing}
                        />
                        <PinButton
                            label={<Delete className="size-5" />}
                            ariaLabel="Effacer le dernier chiffre"
                            onClick={removeDigit}
                            disabled={pin.length === 0 || processing}
                            muted
                        />
                    </div>

                    <div className="min-h-10 text-center text-xs">
                        {hasError ? (
                            <p
                                role="alert"
                                className="font-medium text-red-600"
                            >
                                Code PIN incorrect. Veuillez réessayer.
                            </p>
                        ) : (
                            <p className="text-stone-500">
                                Entrez votre code PIN à 4 chiffres
                            </p>
                        )}
                        {status && <p className="text-emerald-700">{status}</p>}
                    </div>
                </section>
            </main>
        </>
    );
}

function PinButton({
    label,
    ariaLabel,
    onClick,
    disabled,
    muted = false,
}: {
    label: React.ReactNode;
    ariaLabel?: string;
    onClick: () => void;
    disabled: boolean;
    muted?: boolean;
}) {
    return (
        <button
            type="button"
            aria-label={ariaLabel}
            onClick={onClick}
            disabled={disabled}
            className={`flex h-14 items-center justify-center rounded-xl border text-lg font-semibold shadow-sm transition active:scale-95 disabled:cursor-not-allowed disabled:opacity-35 ${
                muted
                    ? 'border-stone-200 bg-white text-stone-400 hover:bg-stone-50'
                    : 'border-stone-200 bg-white text-stone-950 hover:border-amber-300 hover:bg-amber-50'
            }`}
        >
            {label}
        </button>
    );
}
