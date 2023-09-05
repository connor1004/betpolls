import React, { Component } from 'react';
import { Route, Switch } from 'react-router-dom';
import {
  connect
} from 'react-redux';
import { bindActionCreators } from 'redux';
import Layout from '../layouts/Full';
import Api from '../apis/app';
import { login } from '../actions/common';

import GeneralSettings from './Generals/Settings';
import GeneralUsers from './Generals/Users';
import GeneralUserDetail from './Generals/Users/Detail';
import GeneralUserAdd from './Generals/Users/Add';
import BlogsPosts from './Blogs/Posts';
import BlogPostAdd from './Blogs/Posts/Add';
import BlogPostDetail from './Blogs/Posts/Detail';
import AppearancesHome from './Appearances/Home';
import ApperancesMenus from './Appearances/Menus';
import SportsCategories from './Sports/Categories';
import SportsCountries from './Sports/Countries';
import SportLeagues from './Sports/Leagues';
import SportLeagueDetail from './Sports/Leagues/Detail/index';
import SportTeams from './Sports/Teams';
import SportPlayers from './Sports/Players';
import BetGames from './Bets/Games';
import BetGameDetail from './Bets/Games/Detail';
import BetLeaderBoard from './Bets/LeaderBoard';
import ChangePassword from './ChangePassword';
import ManualCategories from './Manual/Categories';
import ManualSubcategories from './Manual/Subcategories';
import ManualCountries from './Manual/Countries';
import ManualCandidateTypes from './Manual/CandidateTypes';
import ManualCandidates from './Manual/Candidates';
import ManualPolls from './Manual/Polls';
import ManualPollDetail from './Manual/Polls/Detail';

class Main extends Component {
  async componentDidMount() {
    const auth = Api.getAuth();
    if (auth && !this.props.auth) {
      await this.props.login(auth);
    }
  }

  render() {
    return (
      <Layout>
        <Switch>
          <Route path="/admin/generals/settings" name="Settings" component={GeneralSettings} />
          <Route path="/admin/generals/users/add" component={GeneralUserAdd} />
          <Route path="/admin/generals/users/:id(\d+)" name="User Detail" component={GeneralUserDetail} />
          <Route path="/admin/generals/users" name="Users" component={GeneralUsers} />
          <Route path="/admin/blogs/posts/add" name="BlogsPostAdd" component={BlogPostAdd} />
          <Route path="/admin/blogs/posts/:id(\d+)" name="BlogsPostDetail" component={BlogPostDetail} />
          <Route path="/admin/blogs/posts" name="BlogsPosts" component={BlogsPosts} />
          <Route path="/admin/appearances/home" name="Home" component={AppearancesHome} />
          <Route path="/admin/appearances/menus" name="Menus" component={ApperancesMenus} />
          <Route path="/admin/sports/categories" name="Categories" component={SportsCategories} />
          <Route path="/admin/sports/countries" name="Countries" component={SportsCountries} />
          <Route path="/admin/sports/leagues/:id(\d+)" name="League Detail" component={SportLeagueDetail} />
          <Route path="/admin/sports/leagues" name="Leagues" component={SportLeagues} />
          <Route path="/admin/sports/teams" name="Teams" component={SportTeams} />
          <Route path="/admin/sports/players" name="Players" component={SportPlayers} />
          <Route path="/admin/bets/games/:id(\d+)" name="Game Detail" component={BetGameDetail} />
          <Route path="/admin/bets/games" name="Games" component={BetGames} />
          <Route path="/admin/bets/leaderboard" name="Games" component={BetLeaderBoard} />
          <Route path="/admin/change-password" name="Change password" component={ChangePassword} />
          <Route path="/admin/manual/categories" name="Manual Categories" component={ManualCategories} />
          <Route path="/admin/manual/subcategories" name="Manual Subcategories" component={ManualSubcategories} />
          <Route path="/admin/manual/countries" name="Manual Countries" component={ManualCountries} />
          <Route path="/admin/manual/candidate-types" name="Manual Candidate Types" component={ManualCandidateTypes} />
          <Route path="/admin/manual/candidates" name="Manual Candidates" component={ManualCandidates} />
          <Route path="/admin/manual/polls/:id(\d+)" name="Manual Poll Detail" component={ManualPollDetail} />
          <Route path="/admin/manual/polls" name="Manual Polls" component={ManualPolls} />
        </Switch>
      </Layout>
    );
  }
}

const mapStateToProps = state => ({
  auth: state.common.auth
});

const mapDispatchToProps = dispatch => ({
  login: bindActionCreators(login, dispatch)
});

export default connect(mapStateToProps, mapDispatchToProps)(Main);
