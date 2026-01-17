import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { PageProps } from '@/types';

interface Joke {
    id: number;
    setup: string;
    punchline: string;
    // Add other properties from your JokeResource
}

interface DashboardProps extends PageProps {
    jokes: {
        data: Joke[];
    };
}

export default function Dashboard({ jokes }: DashboardProps) {
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
                            <h3 className="text-lg font-semibold mb-4">3 Random Jokes</h3>

                            <div className="space-y-4">
                                {jokes?.data?.map((joke) => (
                                    <div key={joke.id} className="p-4 bg-gray-50 rounded-lg">
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
