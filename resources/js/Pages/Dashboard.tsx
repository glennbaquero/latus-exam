import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { PageProps } from '@/types';
import { useState } from 'react';

interface Joke {
    id: number;
    setup: string;
    punchline: string;
    type: string;
}

interface DashboardProps extends PageProps {
    jokes: {
        data: Joke[];
    };
}

export default function Dashboard({ jokes }: DashboardProps) {
    const [isRefreshing, setIsRefreshing] = useState(false);
    const refreshJokes = () => {
        setIsRefreshing(true);
        router.reload({
            onFinish: () => setIsRefreshing(false),
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <div className="flex justify-between items-center mb-4">
                                <h3 className="text-lg font-semibold">Random Jokes</h3>
                                <button
                                    onClick={refreshJokes}
                                    disabled={isRefreshing}
                                    className="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    {isRefreshing ? 'Refreshing...' : 'Get New Jokes'}
                                </button>
                            </div>

                            <h3 className="text-lg font-semibold mb-4">3 Random Jokes</h3>

                            <div className="space-y-4">
                                {jokes?.data?.map((joke) => (
                                    <div key={joke.id} className="p-4 bg-gray-50 rounded-lg">
                                        <p className="text-gray-600 mt-2 text-sm">{joke.type}</p>
                                        <p className="font-medium">{joke.setup}</p>
                                        <p className="text-gray-600 mt-2">{joke.punchline}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
