import Link from "@docusaurus/Link";
import Translate, {translate} from "@docusaurus/Translate";
import useDocusaurusContext from "@docusaurus/useDocusaurusContext";
import CodeBlock from "@theme/CodeBlock";
import Layout from "@theme/Layout";

import styles from "./index.module.css";

export default function Home() {
    const {i18n: {localeConfigs, currentLocale, defaultLocale}} = useDocusaurusContext();
    const llmsUrl = `${localeConfigs[defaultLocale].baseUrl}llms.txt`;
    const arrow = localeConfigs[currentLocale].direction === "rtl" ? "←" : "→";
    const description = translate({
        id: "home.description",
        message: "Install translations and localize routes and Eloquent models with Laravel-Lang.",
    });
    const guides = [
        {
            title: translate({id: "home.guide.translations.title", message: "Manage locales"}),
            description: translate({id: "home.guide.translations.description", message: "Add, update, and remove translations with Artisan."}),
            to: "/translations",
        },
        {
            title: translate({id: "home.guide.configure.title", message: "Configure localization"}),
            description: translate({id: "home.guide.configure.description", message: "Set up locale aliases, localized routes, and model translations."}),
            to: "/configure",
        },
        {
            title: translate({id: "home.guide.plugins.title", message: "Translation plugins"}),
            description: translate({id: "home.guide.plugins.description", message: "Install translation packs or create your own."}),
            to: "/plugins",
        },
        {
            title: translate({id: "home.guide.upgrade.title", message: "Upgrade to Mona"}),
            description: translate({id: "home.guide.upgrade.description", message: "Migrate your dependencies, configuration, and translations."}),
            to: "/upgrade",
        },
    ];

    return (
        <Layout title={translate({id: "home.title", message: "Documentation"})} description={description}>
            <main>
                <section className={styles.hero} aria-labelledby="home-title">
                    <div className={styles.heroInner}>
                        <div className={styles.heroCopy}>
                            <h1 id="home-title"><Translate id="home.hero.title">Localize your Laravel application</Translate></h1>
                            <p className={styles.summary}>{description}</p>
                            <div className={styles.actions}>
                                <Link className="button button--primary button--lg" to="/start">
                                    <Translate id="home.start">Start in 1 minute</Translate>
                                </Link>
                                <Link className="button button--secondary button--lg" to="/legacy">
                                    <Translate id="home.legacy">Legacy documentation</Translate>
                                </Link>
                            </div>
                        </div>
                        <div className={styles.codePreview}>
                            <CodeBlock language="bash" title={translate({id: "home.example.install", message: "Add locales"})}>
                                {"php artisan lang:add fr"}
                            </CodeBlock>
                            <CodeBlock language="php" title={translate({id: "home.example.use", message: "Usage"})}>
                                {"__('auth.failed', locale: 'fr');"}
                            </CodeBlock>
                        </div>
                    </div>
                </section>

                <section className={styles.guides} aria-labelledby="guides-title">
                    <h2 id="guides-title"><Translate id="home.guides">Localization guides</Translate></h2>
                    <div className={styles.guideGrid}>
                        {guides.map((guide) => (
                            <Link key={guide.to} className={styles.guide} to={guide.to}>
                                <h3>{guide.title} <span aria-hidden="true">{arrow}</span></h3>
                                <p>{guide.description}</p>
                            </Link>
                        ))}
                    </div>
                    <a className={styles.compatibility} href={llmsUrl}>
                        <Translate id="home.llms">Documentation for AI agents (llms.txt)</Translate>
                    </a>
                </section>
            </main>
        </Layout>
    );
}
